<?php

namespace Tests\Unit;

use App\Http\Controllers\DocumentBc\DocumentBc41Controller;
use App\Services\BeaCukai\Bc41PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

// Payload CEISA BC 4.1 mengikuti skema RESMI docs/bc_schema/bc41.json (bukan lagi daftar key Villiger di plan §9).
// Pemeriksaan skema memakai opsi yang sama dengan DocumentBc41Controller::masalahKirim (WAJIB_ISI dibaca dari controller).
class Bc41PayloadBuilderTest extends TestCase
{
    private const ROOT_KEYS = [
        'asalData', 'asuransi', 'bruto', 'cif', 'dasarPengenaanPajak', 'kodeJenisTpb', 'freight', 'hargaPenyerahan', 'jabatanTtd',
        'jumlahKontainer', 'kodeDokumen', 'kodeKantor', 'kodeLokasiBayar', 'kodePembayar', 'kodeTujuanPengiriman', 'kotaTtd', 'namaTtd',
        'ndpbm', 'netto', 'nik', 'nilaiBarang', 'nomorAju', 'seri', 'tanggalAju', 'tanggalTtd', 'userPortal', 'volume', 'biayaTambahan',
        'biayaPengurang', 'vd', 'uangMuka', 'ppnPajak', 'ppnbmPajak', 'tarifPpnPajak', 'tarifPpnbmPajak', 'entitas', 'dokumen',
        'pengangkut', 'kontainer', 'kemasan', 'barang',
    ];

    private const BARANG_KEYS = [
        'cif', 'hargaEkspor', 'hargaPenyerahan', 'isiPerKemasan', 'jumlahKemasan', 'jumlahSatuan', 'kodeBarang', 'kodeDokumen',
        'kodeJenisKemasan', 'kodeSatuanBarang', 'merk', 'netto', 'nilaiBarang', 'posTarif', 'seriBarang', 'spesifikasiLain', 'tipe',
        'ukuran', 'uraian', 'volume', 'cifRupiah', 'hargaPerolehan', 'kodeAsalBahanBaku', 'ndpbm', 'uangMuka', 'nilaiJasa', 'bahanBaku',
    ];

    private const BAHAN_BAKU_KEYS = [
        'cif', 'cifRupiah', 'hargaPenyerahan', 'hargaPerolehan', 'jumlahSatuan', 'kodeSatuanBarang', 'kodeAsalBahanBaku', 'kodeBarang',
        'kodeDokAsal', 'kodeDokumen', 'kodeKantor', 'merkBarang', 'ndpbm', 'netto', 'nomorAjuDokAsal', 'nomorDaftarDokAsal', 'posTarif',
        'seriBahanBaku', 'seriBarang', 'seriBarangDokAsal', 'seriIjin', 'spesifikasiLainBarang', 'tanggalDaftarDokAsal', 'tipeBarang',
        'ukuranBarang', 'uraianBarang', 'nilaiJasa',
    ];

    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '00004120261029000001ABCDEF', 'kodeKantor' => '071100', 'kodeJenisTpb' => '1', 'kodeTujuanPengiriman' => '3',
            'ndpbm' => 1.0, 'cif' => 0.0, 'hargaPenyerahan' => 10000000.0, 'uangMuka' => 0.0, 'diskon' => 0.0,
            'dasarPengenaanPajak' => 10000000.0, 'tarifPpnPajak' => 11.0, 'ppnPajak' => 1100000.0, 'tarifPpnbmPajak' => 0.0, 'ppnbmPajak' => 0.0,
            'bruto' => 120.0, 'netto' => 100.0, 'volume' => 2.5, 'kodePembayar' => '3', 'namaPengangkut' => 'TRUK', 'nomorPengangkut' => 'N 1',
            'kotaTtd' => 'Jember', 'tanggalTtd' => '2026-10-29', 'namaTtd' => 'Budi', 'jabatanTtd' => 'Manager', 'userPortal' => 'user.ceisa',
        ], $override);
    }

    private function barang(array $override = []): array
    {
        return array_merge([
            'seriBarang' => 1, 'posTarif' => '84879000', 'kodeBarang' => 'SP-01', 'uraian' => 'BEARING', 'merk' => null, 'tipe' => null,
            'ukuran' => null, 'spesifikasiLain' => null, 'kodeSatuanBarang' => 'PCE', 'jumlahSatuan' => 10.0, 'jumlahKemasan' => 1.0,
            'kodeJenisKemasan' => 'BX', 'netto' => 100.0, 'volume' => 2.5, 'cif' => 10000000.0, 'cifRupiah' => 10000000.0, 'ndpbm' => 1.0,
        ], $override);
    }

    private function bahanBaku(array $override = []): array
    {
        return array_merge([
            'seriBarang' => 1, 'seriBahanBaku' => 1, 'posTarif' => '1234', 'kodeBarang' => 'BB', 'uraianBarang' => 'bahan',
            'kodeSatuanBarang' => 'KGM', 'jumlahSatuan' => 1.0, 'netto' => 0.5, 'kodeDokumen' => '23', 'kodeKantorAsal' => '040300',
            'nomorDaftarDokAsal' => '000123', 'tanggalDaftarDokAsal' => '2026-01-01', 'nomorAjuDokAsal' => '0000230000002026010100001',
            'seriBarangDokAsal' => 1, 'cif' => 10.0, 'cifRupiah' => 10.0, 'hargaPenyerahan' => 10.0, 'hargaPerolehan' => 10.0, 'nilaiJasa' => 0.0,
        ], $override);
    }

    private function entitas(): array
    {
        $pihak = ['namaEntitas' => 'PT X', 'alamatEntitas' => 'Jember', 'nomorIdentitas' => '1234567890123456', 'nibEntitas' => '123',
            'kodeJenisIdentitas' => '6', 'kodeJenisApi' => '02', 'kodeStatus' => '0', 'nomorIjinEntitas' => 'KEP-1', 'tanggalIjinEntitas' => '2020-01-01'];

        return [$pihak, $pihak, $pihak];
    }

    private function children(array $override = []): array
    {
        return array_merge([
            'barang' => [$this->barang()],
            'bahanbaku' => [$this->bahanBaku()],
            'kemasan' => [['seriKemasan' => 1, 'jumlahKemasan' => 2, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']],
            'kontainer' => [],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-01']],
        ], $override);
    }

    private function build(array $header = [], array $children = [], ?array $entitas = null): array
    {
        return (new Bc41PayloadBuilder)->build($this->header($header), $this->children($children), $entitas ?? $this->entitas());
    }

    private function masalah(array $payload, array $opsi = []): array
    {
        return (new CeisaSchemaValidator)->masalah('bc41', $payload, $opsi + [
            'bagian' => [
                'barang' => 'Barang', 'bahanBaku' => 'Bahan Baku', 'dokumen' => 'Dokumen', 'kemasan' => 'Kemasan', 'kontainer' => 'Peti Kemas',
                'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Penerima Barang'], 'pengangkut' => 'Pengangkut',
            ],
            'wajibIsi' => (new ReflectionClass(DocumentBc41Controller::class))->getReflectionConstant('WAJIB_ISI')->getValue(),
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer', 'bahanBaku'],
            'angka' => ['barang.bahanBaku.nilaiJasa'],
        ]);
    }

    private function mengandung(array $masalah, string $potongan): bool
    {
        foreach ($masalah as $m) {
            if (str_contains($m, $potongan)) {
                return true;
            }
        }

        return false;
    }

    public function test_key_lists_follow_the_official_schema(): void
    {
        $p = $this->build();

        $this->assertSame(self::ROOT_KEYS, array_keys($p));
        $this->assertSame(self::BARANG_KEYS, array_keys($p['barang'][0]));
        $this->assertSame(self::BAHAN_BAKU_KEYS, array_keys($p['barang'][0]['bahanBaku'][0]));
        foreach (['disclaimer', 'kodeValuta', 'jaminan', 'pungutan', 'nomorBuktiBayar', 'tanggalBuktiBayar', 'idPengguna'] as $tidakAda) {
            $this->assertArrayNotHasKey($tidakAda, $p);
        }
        foreach (['idBarang', 'diskon', 'freight', 'hargaSatuan', 'kodeAsalBarang', 'kodeKategoriBarang', 'kodeNegaraAsal', 'kodePerhitungan',
            'nilaiDiskon', 'barangTarif', 'barangDokumen'] as $tidakAda) {
            $this->assertArrayNotHasKey($tidakAda, $p['barang'][0]);
        }
    }

    public function test_root_values_and_types(): void
    {
        $p = $this->build();

        $this->assertSame('S', $p['asalData']);
        $this->assertSame('41', $p['kodeDokumen']);
        $this->assertSame('user.ceisa', $p['userPortal']);
        $this->assertSame(2.5, $p['volume']);
        $this->assertSame(10000000.0, $p['nilaiBarang']); // = harga penyerahan
        $this->assertSame('2026-10-29', $p['tanggalAju']); // = tanggalTtd
        $this->assertSame('2026-10-29', $p['tanggalTtd']);
        $this->assertSame('3', $p['kodePembayar']);
        $this->assertSame('1', $p['kodeLokasiBayar']);
        $this->assertSame(0, $p['jumlahKontainer']);
        $this->assertSame(0.0, $p['asuransi']);
        $this->assertSame(0.0, $p['freight']);
    }

    public function test_pengangkut_has_name_number_string_seri_and_numeric_id(): void
    {
        $this->assertSame([['idPengangkut' => 1, 'namaPengangkut' => 'TRUK', 'nomorPengangkut' => 'N 1', 'seriPengangkut' => '1']], $this->build()['pengangkut']);
    }

    public function test_entitas_fixed_slots_with_official_keys(): void
    {
        $e = $this->build()['entitas'];

        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'namaEntitas', 'nibEntitas', 'nomorIdentitas',
            'nomorIjinEntitas', 'seriEntitas', 'tanggalIjinEntitas'], array_keys($e[0]));
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas',
            'nomorIdentitas', 'nomorIjinEntitas', 'seriEntitas', 'tanggalIjinEntitas'], array_keys($e[1]));
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas',
            'nomorIdentitas', 'seriEntitas'], array_keys($e[2]));
        $this->assertSame(['3', '7', '8'], array_column($e, 'kodeEntitas'));
        $this->assertSame([3, 7, 8], array_column($e, 'seriEntitas'));
    }

    public function test_empty_entitas_slot_is_not_shifted(): void
    {
        $pihak = $this->entitas()[0];
        $e = $this->build([], [], [null, null, $pihak])['entitas'];

        $this->assertSame('', $e[0]['namaEntitas']);
        $this->assertSame('', $e[1]['namaEntitas']);
        $this->assertSame('', $e[1]['tanggalIjinEntitas']);
        $this->assertSame('PT X', $e[2]['namaEntitas']);
        $this->assertSame('02', $e[1]['kodeJenisApi']); // nilai bawaan slot kosong
    }

    public function test_rounding_follows_schema_multiples(): void
    {
        $p = $this->build(
            ['cif' => 1234.5678, 'hargaPenyerahan' => 100.1234, 'netto' => 100.12345, 'bruto' => 120.12345, 'ppnPajak' => 11.005, 'tarifPpnPajak' => 11.0],
            ['barang' => [$this->barang(['netto' => 50.12345, 'volume' => 1.23456, 'jumlahSatuan' => 1.23456, 'jumlahKemasan' => 1.236])],
                'bahanbaku' => [$this->bahanBaku(['cif' => 1.239, 'hargaPenyerahan' => 2.345, 'netto' => 0.12345, 'nilaiJasa' => 10.555])]]
        );

        $this->assertSame(1234.57, $p['cif']);
        $this->assertSame(100.12, $p['nilaiBarang']);
        $this->assertSame(100.1234, $p['hargaPenyerahan']);
        $this->assertSame(100.1235, $p['netto']); // 4 desimal (dulu 2)
        $this->assertSame(120.1235, $p['bruto']);
        $this->assertSame(11.01, $p['ppnPajak']);
        $b = $p['barang'][0];
        $this->assertSame(50.1235, $b['netto']);
        $this->assertSame(1.2346, $b['volume']);
        $this->assertSame(1.2346, $b['jumlahSatuan']);
        $this->assertSame(1.24, $b['jumlahKemasan']);
        $bb = $b['bahanBaku'][0];
        $this->assertSame(1.24, $bb['cif']);
        $this->assertSame(2.35, $bb['hargaPenyerahan']);
        $this->assertSame(0.1235, $bb['netto']);
        $this->assertSame(10.56, $bb['nilaiJasa']); // angka hingga 2 desimal, bukan integer
    }

    public function test_kemasan_and_dokumen_keys(): void
    {
        $p = $this->build();

        $this->assertSame(['jumlahKemasan', 'kodeJenisKemasan', 'merkKemasan', 'seriKemasan'], array_keys($p['kemasan'][0]));
        $this->assertSame(['idDokumen', 'kodeDokumen', 'nomorDokumen', 'seriDokumen', 'tanggalDokumen'], array_keys($p['dokumen'][0]));
    }

    public function test_complete_payload_passes_the_official_schema(): void
    {
        $this->assertSame([], $this->masalah($this->build()));
    }

    public function test_decimal_bahan_baku_nilai_jasa_needs_the_angka_option(): void
    {
        $p = $this->build([], ['bahanbaku' => [$this->bahanBaku(['nilaiJasa' => 10.5])]]);

        $this->assertSame([], $this->masalah($p));
        $this->assertTrue($this->mengandung($this->masalah($p, ['angka' => []]), 'Bahan Baku #1 > Nilai Jasa (nilaiJasa): harus berupa bilangan bulat'));
    }

    public function test_missing_pemilik_izin_and_pengangkut_are_reported(): void
    {
        $e = $this->entitas();
        unset($e[1]['nomorIjinEntitas'], $e[1]['tanggalIjinEntitas']);
        $m = $this->masalah($this->build(['namaPengangkut' => '', 'nomorPengangkut' => ''], [], $e));

        $this->assertTrue($this->mengandung($m, 'Pemilik Barang > Nomor Izin (nomorIjinEntitas): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Pemilik Barang > Tanggal Izin (tanggalIjinEntitas): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Pengangkut #1 > Nama Sarana Pengangkut (namaPengangkut): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Pengangkut #1 > Nomor Sarana Pengangkut (nomorPengangkut): wajib diisi'));
    }

    public function test_empty_dates_nomor_aju_and_dokumen_rows_are_reported(): void
    {
        $m = $this->masalah($this->build(
            ['nomorAju' => '000041-AJU-1', 'tanggalTtd' => null, 'kodePembayar' => ''],
            ['dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => null, 'tanggalDokumen' => null]], 'kemasan' => [
                ['seriKemasan' => 1, 'jumlahKemasan' => 0, 'kodeJenisKemasan' => '', 'merkKemasan' => ''],
            ]]
        ));

        $this->assertTrue($this->mengandung($m, 'Nomor AJU (nomorAju): harus tepat 26'));
        $this->assertTrue($this->mengandung($m, 'Tanggal Aju (tanggalAju): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Wajib Bayar (kodePembayar): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Dokumen #1 > Nomor (nomorDokumen): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Dokumen #1 > Tanggal (tanggalDokumen): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Kemasan #1 > Jenis Kemasan (kodeJenisKemasan): wajib diisi'));
    }

    // Bahan baku per barang tidak diwajibkan; barang tanpa bahan baku tetap lolos.
    public function test_barang_without_bahan_baku_passes(): void
    {
        $this->assertSame([], $this->masalah($this->build([], ['bahanbaku' => []])));
    }
}
