<?php

namespace Tests\Unit;

use App\Services\BeaCukai\Bc25PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

// Payload CEISA BC 2.5 mengikuti skema RESMI docs/bc_schema/bc25.json (bukan lagi daftar key Villiger di plan §9).
class Bc25PayloadBuilderTest extends TestCase
{
    private const ROOT_KEYS = [
        'asalData', 'bruto', 'cif', 'dasarPengenaanPajak', 'disclaimer', 'hargaPenyerahan', 'idPengguna', 'jabatanTtd', 'jumlahKontainer',
        'kodeDokumen', 'kodeKantor', 'kodeLokasiBayar', 'kodeTujuanPengiriman', 'kodeJenisTpb', 'kodeCaraBayar', 'kodeValuta',
        'kotaTtd', 'namaTtd', 'ndpbm', 'netto', 'nomorAju', 'ppnPajak', 'ppnbmPajak', 'seri', 'tanggalAju', 'tanggalTtd',
        'tarifPpnPajak', 'tarifPpnbmPajak', 'volume', 'barang', 'dokumen', 'entitas', 'kemasan', 'kontainer', 'pengangkut',
    ];

    private const BARANG_KEYS = [
        'cif', 'cifRupiah', 'diskon', 'fob', 'freight', 'hargaEkspor', 'hargaPenyerahan', 'hargaPerolehan', 'isiPerKemasan',
        'jumlahKemasan', 'jumlahSatuan', 'kodeBarang', 'kodeDokAsal', 'kodeGunaBarang', 'kodeJenisKemasan', 'kodeKategoriBarang',
        'kodeKondisiBarang', 'kodePerhitungan', 'kodeSatuanBarang', 'merk', 'ndpbm', 'netto', 'nilaiBarang', 'posTarif',
        'seriBarang', 'spesifikasiLain', 'tipe', 'ukuran', 'uraian', 'bahanBaku', 'barangTarif', 'barangDokumen',
    ];

    private const BAHAN_BAKU_KEYS = [
        'cif', 'cifRupiah', 'hargaPenyerahan', 'hargaPerolehan', 'jumlahSatuan', 'kodeSatuanBarang', 'kodeAsalBahanBaku', 'kodeBarang',
        'kodeDokAsal', 'kodeKantor', 'merkBarang', 'ndpbm', 'nomorAjuDokAsal', 'nomorDaftarDokAsal', 'posTarif', 'seriBahanBaku',
        'seriBarang', 'seriBarangDokAsal', 'seriIjin', 'spesifikasiLainBarang', 'tanggalDaftarDokAsal', 'tipeBarang', 'ukuranBarang',
        'uraianBarang', 'bahanBakuTarif',
    ];

    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '00002520261029000001ABCDEF', 'kodeKantor' => '040300', 'kodeJenisTpb' => '1', 'kodeTujuanPengiriman' => '3',
            'kodeCaraBayar' => '1', 'kodeCaraAngkut' => '3', 'kodeValuta' => 'IDR', 'ndpbm' => 1.0, 'cif' => 0.0,
            'hargaPenyerahan' => 10000000.0, 'dasarPengenaanPajak' => 10000000.0, 'tarifPpnPajak' => 11.0, 'ppnPajak' => 1100000.0,
            'tarifPpnbmPajak' => 0.0, 'ppnbmPajak' => 0.0, 'bruto' => 120.0, 'netto' => 100.0, 'volume' => 2.5,
            'namaPengangkut' => 'TRUK 1', 'nomorPengangkut' => 'N 1', 'kotaTtd' => 'Jember', 'tanggalTtd' => '2026-10-26',
            'namaTtd' => 'Budi', 'jabatanTtd' => 'Manager', 'idPengguna' => 'user.ceisa',
        ], $override);
    }

    private function barang(int $seri, array $override = []): array
    {
        return array_merge([
            'seriBarang' => $seri, 'posTarif' => '84879000', 'kodeBarang' => 'SP-0'.$seri, 'uraian' => 'BEARING', 'merk' => null,
            'tipe' => null, 'ukuran' => null, 'spesifikasiLain' => null, 'kodeSatuanBarang' => 'PCE', 'jumlahSatuan' => 10.0,
            'jumlahKemasan' => 1.0, 'kodeJenisKemasan' => 'BX', 'netto' => 100.0, 'cif' => 10000000.0, 'cifRupiah' => 10000000.0,
            'ndpbm' => 0.0, 'kodeKategoriBarang' => '1', 'kodeGunaBarang' => '0', 'kodeKondisiBarang' => '1', 'kodeDokAsal' => '23',
            'persenBM' => 5.0, 'persenPPN' => 11.0, 'persenPPh' => 2.5,
        ], $override);
    }

    private function bahanBaku(array $override = []): array
    {
        return array_merge([
            'seriBarang' => 1, 'seriBahanBaku' => 1, 'posTarif' => '1234', 'kodeBarang' => 'BB', 'uraianBarang' => 'bahan',
            'kodeSatuanBarang' => 'KGM', 'jumlahSatuan' => 1.0, 'netto' => 0.5, 'kodeDokumen' => '23', 'kodeKantorAsal' => '040300',
            'nomorDaftarDokAsal' => '000123', 'tanggalDaftarDokAsal' => '2026-01-01', 'nomorAjuDokAsal' => null,
            'seriBarangDokAsal' => 3, 'cif' => 10.0, 'cifRupiah' => 10.0, 'hargaPenyerahan' => 10.0, 'hargaPerolehan' => 10.0,
        ], $override);
    }

    private function entitas(): array
    {
        return [
            ['namaEntitas' => 'PT AJC', 'alamatEntitas' => 'Jember', 'nomorIdentitas' => '1234567890123456', 'nibEntitas' => '123',
                'nomorIjinEntitas' => 'KEP-1', 'tanggalIjinEntitas' => '2020-01-01', 'kodeJenisApi' => '02', 'kodeJenisIdentitas' => '6', 'kodeStatus' => '5'],
            ['namaEntitas' => 'PT PEMILIK', 'alamatEntitas' => 'Surabaya', 'nomorIdentitas' => '1234567890123456', 'kodeJenisIdentitas' => '6',
                'kodeStatus' => '0', 'kodeNegara' => 'ID'],
            ['namaEntitas' => 'PT PENERIMA', 'alamatEntitas' => 'Malang', 'nomorIdentitas' => '1234567890123456', 'kodeJenisIdentitas' => '6',
                'kodeStatus' => '0', 'kodeJenisApi' => null, 'niperEntitas' => 'NIP-9', 'nomorIjinEntitas' => 'IZ', 'tanggalIjinEntitas' => '2021-01-01'],
        ];
    }

    private function build(array $header = [], array $children = [], ?array $entitas = null): array
    {
        $children += [
            'barang' => [$this->barang(1)], 'bahanbaku' => [], 'kemasan' => [], 'kontainer' => [],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-01']],
        ];

        return (new Bc25PayloadBuilder)->build($this->header($header), $children, $entitas ?? $this->entitas());
    }

    public function test_root_key_order_follows_official_schema(): void
    {
        $p = $this->build();

        $this->assertSame(self::ROOT_KEYS, array_keys($p));
        foreach (['asuransi', 'freight', 'biayaTambahan', 'biayaPengurang', 'uangMuka', 'nilaiBarang', 'nik', 'vd', 'jaminan', 'pungutan'] as $tidakAda) {
            $this->assertArrayNotHasKey($tidakAda, $p);
        }
    }

    public function test_root_values_and_types(): void
    {
        $p = $this->build(['kodeKantor' => '040300']);

        $this->assertSame('S', $p['asalData']);
        $this->assertSame('25', $p['kodeDokumen']);
        $this->assertSame('1', $p['kodeLokasiBayar']);
        $this->assertSame('1', $p['kodeCaraBayar']);
        $this->assertSame('1', $p['kodeJenisTpb']);
        $this->assertSame('1', $p['disclaimer']);
        $this->assertSame(1, $p['seri']);
        $this->assertSame(2.5, $p['volume']);
        $this->assertSame(0.0, $p['cif']);
        $this->assertSame(1100000.0, $p['ppnPajak']);
        $this->assertSame(11.0, $p['tarifPpnPajak']);
        $this->assertSame('2026-10-26', $p['tanggalAju']);
        $this->assertSame('2026-10-26', $p['tanggalTtd']);
        $this->assertSame(0, $p['jumlahKontainer']);
    }

    public function test_jumlah_kontainer_is_count_and_volume_defaults_to_zero(): void
    {
        $p = $this->build(['volume' => null], ['kontainer' => [
            ['seriKontainer' => 1, 'nomorKontainer' => 'A', 'kodeUkuranKontainer' => '20', 'kodeJenisKontainer' => '8', 'kodeTipeKontainer' => '1'],
            ['seriKontainer' => 2, 'nomorKontainer' => 'B', 'kodeUkuranKontainer' => '40', 'kodeJenisKontainer' => '8', 'kodeTipeKontainer' => '1'],
        ]]);

        $this->assertSame(2, $p['jumlahKontainer']);
        $this->assertSame(['kodeJenisKontainer', 'kodeTipeKontainer', 'kodeUkuranKontainer', 'nomorKontainer', 'seriKontainer'], array_keys($p['kontainer'][0]));
        $this->assertSame(0.0, $p['volume']);
    }

    public function test_root_amounts_rounded_to_two_decimals(): void
    {
        $p = $this->build(['cif' => 1234.5678, 'ppnPajak' => 1100000.456, 'dasarPengenaanPajak' => 10000000.004, 'tarifPpnPajak' => 11.005]);

        $this->assertSame(1234.57, $p['cif']);
        $this->assertSame(1100000.46, $p['ppnPajak']);
        $this->assertSame(10000000.0, $p['dasarPengenaanPajak']);
        $this->assertSame(11.01, $p['tarifPpnPajak']);
    }

    public function test_netto_keeps_four_decimals(): void
    {
        // Skema: netto kelipatan 0.0001; dibulatkan 2 desimal akan mengubah 100.4567 menjadi 100.46.
        $p = $this->build(['netto' => 100.45678], ['barang' => [$this->barang(1, ['netto' => 50.12345])]]);

        $this->assertSame(100.4568, $p['netto']);
        $this->assertSame(50.1235, $p['barang'][0]['netto']);
        $this->assertSame(50.1235, $p['barang'][0]['barangTarif'][0]['jumlahSatuan']);
        $this->assertSame(0.0, $this->build(['netto' => null])['netto']);
    }

    public function test_entitas_fixed_slots_and_official_keys(): void
    {
        $e = $this->build()['entitas'];

        $this->assertSame([3, 7, 8], array_column($e, 'seriEntitas'));
        $this->assertSame(['3', '7', '8'], array_column($e, 'kodeEntitas'));
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas', 'nomorIdentitas', 'seriEntitas',
            'kodeJenisApi', 'nomorIjinEntitas', 'tanggalIjinEntitas', 'nibEntitas'], array_keys($e[0]));
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas', 'nomorIdentitas', 'seriEntitas'], array_keys($e[1]));
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas', 'nomorIdentitas', 'seriEntitas',
            'kodeJenisApi', 'niperEntitas'], array_keys($e[2]));
        $this->assertSame('NIP-9', $e[2]['niperEntitas']);
        $this->assertSame('02', $e[2]['kodeJenisApi']);

        // Slot kosong tidak menggeser yang lain.
        $kosong = $this->entitas();
        $kosong[1] = null;
        $e = $this->build([], [], $kosong)['entitas'];
        $this->assertSame('', $e[1]['namaEntitas']);
        $this->assertSame('PT PENERIMA', $e[2]['namaEntitas']);
    }

    public function test_barang_keys_and_values(): void
    {
        $b = $this->build()['barang'][0];

        $this->assertSame(self::BARANG_KEYS, array_keys($b));
        $this->assertSame(0, $b['fob']);
        $this->assertSame('0', $b['kodeGunaBarang']);
        $this->assertSame('1', $b['kodeKondisiBarang']);
        $this->assertSame('23', $b['kodeDokAsal']);
        $this->assertSame('1', $b['kodeKategoriBarang']);
        $this->assertSame('-', $b['merk']);
        $this->assertSame('0', $b['tipe']);
        $this->assertSame(0, $b['ndpbm']); // skema: integer
        $this->assertSame('0', $b['kodePerhitungan']);
        $this->assertSame([], $b['barangDokumen']);
        foreach (['idBarang', 'hargaSatuan', 'kodeAsalBarang', 'kodeAsalBahanBaku', 'kodeDokumen', 'kodeNegaraAsal', 'nilaiDiskon', 'nilaiJasa', 'uangMuka'] as $tidakAda) {
            $this->assertArrayNotHasKey($tidakAda, $b);
        }
    }

    public function test_barang_tarif_computed_from_persen(): void
    {
        $t = $this->build()['barang'][0]['barangTarif'];

        $this->assertSame(['BM', 'PPN', 'PPH'], array_column($t, 'kodeJenisPungutan'));
        $this->assertSame([5.0, 11.0, 2.5], array_column($t, 'tarif'));
        // BM = 10.000.000 x 5% = 500.000; dasar PPN/PPh = 10.500.000.
        $this->assertSame([500000.0, 1155000.0, 262500.0], array_column($t, 'nilaiBayar'));
        $this->assertSame([1, 2, 3], array_column($t, 'seriBarang'));
    }

    public function test_bahan_baku_keys_stored_seri_and_empty_tarif(): void
    {
        $bb = $this->build([], ['bahanbaku' => [$this->bahanBaku()]])['barang'][0]['bahanBaku'][0];

        $this->assertSame(self::BAHAN_BAKU_KEYS, array_keys($bb));
        $this->assertSame([], $bb['bahanBakuTarif']);
        $this->assertSame(3, $bb['seriBarangDokAsal']);
        $this->assertSame('', $bb['nomorAjuDokAsal']); // string wajib, bukan null
        $this->assertSame('23', $bb['kodeDokAsal']);
        $this->assertSame('0', $bb['kodeAsalBahanBaku']);
        $this->assertSame(1, $this->build([], ['bahanbaku' => [$this->bahanBaku(['seriBarangDokAsal' => null])]])['barang'][0]['bahanBaku'][0]['seriBarangDokAsal']);
    }

    public function test_pengangkut_and_kemasan_keys(): void
    {
        $p = $this->build([], ['kemasan' => [['seriKemasan' => 1, 'jumlahKemasan' => 2, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']]]);

        $this->assertSame(['namaPengangkut', 'nomorPengangkut', 'kodeCaraAngkut', 'seriPengangkut'], array_keys($p['pengangkut'][0]));
        $this->assertSame('3', $p['pengangkut'][0]['kodeCaraAngkut']);
        $this->assertSame(['jumlahKemasan', 'kodeJenisKemasan', 'seriKemasan', 'merkKemasan'], array_keys($p['kemasan'][0]));
    }

    public function test_valuta_translated_to_iso(): void
    {
        $container = Container::getInstance();
        $container->instance('config', new Repository(['beacukai' => ['valuta_iso' => ['RM' => 'MYR', 'EURO' => 'EUR']]]));
        try {
            $this->assertSame('MYR', $this->build(['kodeValuta' => 'RM'])['kodeValuta']);
            $this->assertSame('EUR', $this->build(['kodeValuta' => 'EURO'])['kodeValuta']);
            $this->assertSame('IDR', $this->build(['kodeValuta' => 'IDR'])['kodeValuta']);
        } finally {
            $container->forgetInstance('config');
        }
    }

    // Dokumen lengkap lolos skema resmi; idPengguna = username CEISA dari header.
    public function test_complete_payload_passes_the_official_schema(): void
    {
        $p = $this->build([], ['bahanbaku' => [$this->bahanBaku(['nomorAjuDokAsal' => '0000230000002026010100001'])], 'kemasan' => [
            ['seriKemasan' => 1, 'jumlahKemasan' => 2, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => ''],
        ]]);
        $masalah = (new CeisaSchemaValidator)->masalah('bc25', $p, ['sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer', 'bahanBaku']]);

        $this->assertSame([], $masalah);
        $this->assertSame('user.ceisa', $p['idPengguna']);
    }

    public function test_encode_keeps_zero_fractions(): void
    {
        $json = (new Bc25PayloadBuilder)->encode($this->build());

        $this->assertStringStartsWith('{"asalData":"S","bruto":120.0,"cif":0.0,', $json);
        $this->assertStringContainsString('"fob":0,', $json);
    }
}
