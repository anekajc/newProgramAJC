<?php

namespace Tests\Unit;

use App\Services\BeaCukai\Bc40PayloadBuilder;
use PHPUnit\Framework\TestCase;

// Payload CEISA BC 4.0 harus sama persis dengan Villiger (urutan key + tipe), lihat
// docs/documentbc40_plan.md §9-§10.
class Bc40PayloadBuilderTest extends TestCase
{
    // Perhatikan array yang diselang-seling dengan skalar (entitas ... barang, lalu idPengguna ... nilaiJasa).
    private const ROOT_KEYS = [
        'asalData', 'asuransi', 'bruto', 'kodeJenisTpb', 'hargaPenyerahan', 'jabatanTtd', 'kodeDokumen', 'kodeKantor',
        'kodeTujuanPengiriman', 'kotaTtd', 'namaTtd', 'netto', 'nomorAju', 'seri', 'tanggalAju', 'tanggalTtd', 'entitas',
        'pengangkut', 'kemasan', 'pungutan', 'barang', 'idPengguna', 'jumlahKontainer', 'nik', 'volume', 'biayaTambahan',
        'biayaPengurang', 'vd', 'uangMuka', 'nilaiJasa', 'dokumen', 'kontainer',
    ];

    private const BARANG_KEYS = [
        'asuransi', 'bruto', 'cif', 'diskon', 'hargaEkspor', 'hargaPenyerahan', 'hargaSatuan',
        'jumlahKemasan', 'jumlahRealisasi', 'jumlahSatuan', 'kodeBarang', 'kodeDokumen', 'kodeJenisKemasan',
        'kodeSatuanBarang', 'merk', 'netto', 'nilaiBarang', 'posTarif', 'seriBarang', 'spesifikasiLain', 'tipe', 'ukuran',
        'uraian', 'volume', 'cifRupiah', 'hargaPerolehan', 'kodeAsalBahanBaku', 'ndpbm', 'uangMuka', 'nilaiJasa',
        'barangTarif',
    ];

    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '000040-AJU-1',
            'kodeKantor' => '071100',
            'kodeJenisTpb' => '1',
            'kodeTujuanPengirim' => '1',
            'tanggalAju' => '2026-10-26',
            'hargaPenyerahan' => 15000000.0,
            'nilaiJasa' => 0.0,
            'uangMuka' => 0.0,
            'bruto' => 520.0,
            'netto' => 500.0,
            'volume' => 0.0,
            'namaPengangkut' => 'TRUCK',
            'nomorPengangkut' => 'P 1234 AB',
            'kodeJenisKemasan' => '',
            'jumlahKemasan' => 0,
            'merkKemasan' => '',
            'kotaTtd' => 'Jember',
            'tanggalTtd' => '2026-10-27',
            'namaTtd' => 'Budi',
            'jabatanTtd' => 'Manager',
            'idPengguna' => 'erp_user',
        ], $override);
    }

    private function barang(int $seri, array $override = []): array
    {
        return array_merge([
            'seriBarang' => $seri,
            'posTarif' => '84879000',
            'kodeBarang' => 'SP-00'.$seri,
            'uraian' => 'BEARING',
            'merk' => '',
            'tipe' => '',
            'ukuran' => '',
            'spesifikasiLain' => '',
            'kodeSatuanBarang' => 'PCE',
            'jumlahSatuan' => 100.0,
            'kodeJenisKemasan' => 'BX',
            'jumlahKemasan' => 10,
            'netto' => 500.0,
            'volume' => 0.0,
            'hargaPenyerahan' => 15000000.0,
        ], $override);
    }

    private function entitas(): array
    {
        return [
            ['namaEntitas' => 'PT TPB', 'alamatEntitas' => 'Jember', 'nomorIdentitas' => '01.234', 'nibEntitas' => 'NIB1',
                'nomorIjinEntitas' => 'IZIN-1', 'tanggalIjinEntitas' => '2020-01-02', 'kodeJenisIdentitas' => '6', 'kodeJenisApi' => '02', 'kodeStatus' => '0'],
            ['namaEntitas' => 'PT PEMILIK', 'alamatEntitas' => 'Surabaya', 'nomorIdentitas' => '09.876'],
            ['namaEntitas' => 'PT PENGIRIM', 'alamatEntitas' => 'Malang', 'nomorIdentitas' => '', 'kodeJenisApi' => null, 'kodeStatus' => null],
        ];
    }

    private function build(array $header = [], array $barang = [], array $dokumen = []): array
    {
        return (new Bc40PayloadBuilder)->build(
            $this->header($header),
            ['barang' => $barang ?: [$this->barang(1)], 'dokumen' => $dokumen ?: [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-20']]],
            $this->entitas()
        );
    }

    public function test_root_key_order_is_interleaved(): void
    {
        $this->assertSame(self::ROOT_KEYS, array_keys($this->build()));
    }

    public function test_root_values_and_types(): void
    {
        $p = $this->build();

        $this->assertSame('S', $p['asalData']);
        $this->assertSame('40', $p['kodeDokumen']);
        $this->assertSame(0.0, $p['asuransi']);
        $this->assertSame(520.0, $p['bruto']);
        $this->assertSame(15000000.0, $p['hargaPenyerahan']);
        $this->assertSame('1', $p['kodeJenisTpb']);
        $this->assertSame('1', $p['kodeTujuanPengiriman']); // dari kodeTujuanPengirim
        $this->assertSame(1, $p['seri']);
        $this->assertSame('2026-10-26', $p['tanggalAju']);
        $this->assertSame('erp_user', $p['idPengguna']);
        $this->assertSame(0, $p['jumlahKontainer']);
        $this->assertSame('', $p['nik']); // BC 2.3 / 2.6.1 mengirim "0"
        $this->assertSame(0.0, $p['volume']);
        $this->assertSame(0.0, $p['biayaTambahan']);
        $this->assertSame(0.0, $p['biayaPengurang']);
        $this->assertSame(0.0, $p['vd']);
        $this->assertSame(0.0, $p['uangMuka']);
        $this->assertSame(0.0, $p['nilaiJasa']);
        $this->assertSame([], $p['kontainer']);
    }

    // Tanpa fallback "": tanggal kosong dikirim null; teks tanpa fallback ikut null (raw).
    public function test_empty_dates_and_raw_text_are_null(): void
    {
        $p = $this->build(['tanggalAju' => null, 'tanggalTtd' => '', 'kodeKantor' => null, 'namaTtd' => null, 'nomorAju' => null, 'kodeJenisTpb' => null]);

        $this->assertNull($p['tanggalAju']);
        $this->assertNull($p['tanggalTtd']);
        $this->assertNull($p['kodeKantor']);
        $this->assertNull($p['namaTtd']);
        $this->assertNull($p['nomorAju']);
        $this->assertNull($p['kodeJenisTpb']);
        $this->assertSame('', $this->build(['kodeTujuanPengirim' => ''])['kodeTujuanPengiriman']); // fallback ""
    }

    public function test_entitas_slots_three_seven_nine(): void
    {
        $e = $this->build()['entitas'];

        $this->assertCount(3, $e);
        // Slot 3: seriEntitas SEBELUM tanggalIjinEntitas
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'namaEntitas', 'nibEntitas', 'nomorIdentitas',
            'nomorIjinEntitas', 'seriEntitas', 'tanggalIjinEntitas'], array_keys($e[0]));
        $slot = ['alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas', 'nibEntitas',
            'nomorIdentitas', 'seriEntitas'];
        $this->assertSame($slot, array_keys($e[1]));
        $this->assertSame($slot, array_keys($e[2]));

        $this->assertSame(['3', '7', '9'], array_column($e, 'kodeEntitas'));
        $this->assertSame([3, 7, 9], array_column($e, 'seriEntitas'));
        $this->assertSame('2020-01-02', $e[0]['tanggalIjinEntitas']);
        $this->assertSame('PT PEMILIK', $e[1]['namaEntitas']);
        $this->assertSame('02', $e[2]['kodeJenisApi']);
        $this->assertSame('0', $e[2]['kodeStatus']);
        $this->assertSame('6', $e[2]['kodeJenisIdentitas']);
    }

    // Skema resmi: slot tetap (Pengusaha 3, Pemilik 7, Pengirim 9); slot kosong tidak digeser.
    public function test_entitas_slots_are_fixed_and_empty_slot_stays_blank(): void
    {
        $entitas = $this->entitas();
        $e = (new Bc40PayloadBuilder)->build($this->header(), ['barang' => [$this->barang(1)]], [null, null, $entitas[2]])['entitas'];

        $this->assertSame('', $e[0]['namaEntitas']); // Pengusaha tetap kosong, tidak diisi pengirim
        $this->assertSame('', $e[0]['tanggalIjinEntitas']);
        $this->assertSame('', $e[1]['namaEntitas']);
        $this->assertSame('PT PENGIRIM', $e[2]['namaEntitas']); // pengirim tetap di slot seri 9
        $this->assertSame([3, 7, 9], array_column($e, 'seriEntitas'));
        $this->assertSame('02', $e[1]['kodeJenisApi']); // slot kosong: nilai bawaan
    }

    public function test_barang_key_order_and_values(): void
    {
        $b = $this->build()['barang'][0];

        $this->assertSame(self::BARANG_KEYS, array_keys($b));
        $this->assertSame(0.0, $b['asuransi']);
        $this->assertSame(520.0, $b['bruto']); // satu barang menampung seluruh bruto header
        $this->assertSame(0.0, $b['cif']);
        $this->assertSame(15000000.0, $b['hargaPenyerahan']);
        $this->assertSame(100.0, $b['jumlahRealisasi']); // jumlah realisasi = jumlah satuan
        $this->assertArrayNotHasKey('isiPerkemasan', $b);
        $this->assertArrayNotHasKey('isiPerKemasan', $b);
        $this->assertSame(10, $b['jumlahKemasan']);
        $this->assertSame(100.0, $b['jumlahSatuan']);
        $this->assertSame('40', $b['kodeDokumen']);
        $this->assertSame('', $b['merk']);
        $this->assertSame(500.0, $b['netto']);
        $this->assertSame(15000000.0, $b['nilaiBarang']); // = harga penyerahan
        $this->assertSame(0.0, $b['cifRupiah']);
        $this->assertSame('1', $b['kodeAsalBahanBaku']);
        $this->assertSame(0.0, $b['uangMuka']);
        $this->assertSame(0.0, $b['nilaiJasa']);
    }

    public function test_single_barang_takes_whole_header_amounts(): void
    {
        $b = $this->build(['uangMuka' => 2500.0, 'nilaiJasa' => 125.5])['barang'][0];

        $this->assertSame(2500.0, $b['uangMuka']);
        $this->assertSame(125.5, $b['nilaiJasa']);
    }

    // bruto sebanding netto, uang muka / nilai jasa sebanding harga; baris terakhir menampung sisa pembulatan.
    public function test_header_amounts_are_split_across_barang(): void
    {
        $p = $this->build(
            ['bruto' => 100.0, 'uangMuka' => 1000.0, 'nilaiJasa' => 10.0],
            [
                $this->barang(1, ['netto' => 10.0, 'hargaPenyerahan' => 100.0]),
                $this->barang(2, ['netto' => 10.0, 'hargaPenyerahan' => 100.0]),
                $this->barang(3, ['netto' => 10.0, 'hargaPenyerahan' => 100.0]),
            ]
        )['barang'];

        $this->assertSame([33.3333, 33.3333, 33.3334], array_column($p, 'bruto'));
        $this->assertSame([333.3333, 333.3333, 333.3334], array_column($p, 'uangMuka'));
        $this->assertSame([3.3333, 3.3333, 3.3334], array_column($p, 'nilaiJasa'));
        $this->assertEqualsWithDelta(100.0, array_sum(array_column($p, 'bruto')), 1e-9);
    }

    public function test_split_follows_weights_and_falls_back_to_equal_when_weights_are_zero(): void
    {
        $p = $this->build(
            ['bruto' => 90.0, 'uangMuka' => 300.0],
            [
                $this->barang(1, ['netto' => 10.0, 'hargaPenyerahan' => 100.0]),
                $this->barang(2, ['netto' => 20.0, 'hargaPenyerahan' => 200.0]),
            ]
        )['barang'];
        $this->assertSame([30.0, 60.0], array_column($p, 'bruto'));
        $this->assertSame([100.0, 200.0], array_column($p, 'uangMuka'));

        $nol = $this->build(
            ['bruto' => 10.0],
            [$this->barang(1, ['netto' => 0.0]), $this->barang(2, ['netto' => 0.0])]
        )['barang'];
        $this->assertSame([5.0, 5.0], array_column($nol, 'bruto'));
    }

    public function test_barang_tarif_single_ppn_with_last_key(): void
    {
        $t = $this->build([], [$this->barang(1), $this->barang(2)])['barang'];

        foreach ([0, 1] as $i) {
            $this->assertCount(1, $t[$i]['barangTarif']);
            $this->assertSame(['kodeJenisTarif', 'jumlahSatuan', 'kodeFasilitasTarif', 'kodeSatuanBarang', 'nilaiBayar', 'nilaiFasilitas',
                'nilaiSudahDilunasi', 'seriBarang', 'tarif', 'tarifFasilitas', 'kodeJenisPungutan'], array_keys($t[$i]['barangTarif'][0]));
            $this->assertSame($i + 1, $t[$i]['barangTarif'][0]['seriBarang']); // seri barangnya sendiri
        }
        $tarif = $t[0]['barangTarif'][0];
        $this->assertSame('PPN', $tarif['kodeJenisPungutan']);
        $this->assertSame(11, $tarif['tarif']);
        $this->assertSame(500.0, $tarif['jumlahSatuan']);
        $this->assertSame(0, $tarif['nilaiBayar']);
        $this->assertSame('3', $tarif['kodeFasilitasTarif']);
    }

    // Nomor / tanggal kosong dikirim "" (bukan null) supaya tercatat "wajib diisi" saat kirim.
    public function test_dokumen_has_no_id_key(): void
    {
        $d = $this->build([], [], [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => null, 'tanggalDokumen' => null]])['dokumen'];

        $this->assertSame(['kodeDokumen', 'nomorDokumen', 'seriDokumen', 'tanggalDokumen'], array_keys($d[0]));
        $this->assertSame('', $d[0]['nomorDokumen']);
        $this->assertSame('', $d[0]['tanggalDokumen']);
    }

    public function test_pengangkut_and_pungutan(): void
    {
        $p = $this->build();

        $this->assertSame([['namaPengangkut' => 'TRUCK', 'nomorPengangkut' => 'P 1234 AB', 'seriPengangkut' => 1]], $p['pengangkut']);
        $this->assertSame([['kodeFasilitasTarif' => '3', 'kodeJenisPungutan' => 'PPN', 'nilaiPungutan' => 0]], $p['pungutan']);
    }

    // BC 4.0: jumlah, jenis, MERK, seri (BC 2.3: jumlah, jenis, seri, merk).
    public function test_kemasan_only_when_filled_with_merk_before_seri(): void
    {
        $this->assertSame([], $this->build()['kemasan']);

        $k = $this->build(['jumlahKemasan' => 10, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '-'])['kemasan'];
        $this->assertSame([['jumlahKemasan' => 10, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '-', 'seriKemasan' => 1]], $k);

        // jumlahKemasan integer wajib: 0 tetap 0 (bukan null); jenis kosong tetap "" (dicatat saat kirim).
        $this->assertSame(0, $this->build(['kodeJenisKemasan' => 'BX'])['kemasan'][0]['jumlahKemasan']);
        $this->assertSame('', $this->build(['jumlahKemasan' => 3])['kemasan'][0]['kodeJenisKemasan']);
    }

    public function test_barang_sorted_by_seri_and_lists_reindexed(): void
    {
        $p = $this->build([], [$this->barang(2, ['kodeBarang' => 'B']), $this->barang(1, ['kodeBarang' => 'A'])]);

        $this->assertSame(['A', 'B'], array_column($p['barang'], 'kodeBarang'));
        $this->assertSame('[', substr(json_encode($p['barang']), 0, 1));
    }

    public function test_empty_numbers_fall_back_to_zero_and_nulls_stay(): void
    {
        $p = $this->build(['bruto' => null, 'netto' => 0, 'hargaPenyerahan' => null], [$this->barang(1, ['netto' => null, 'jumlahKemasan' => null, 'hargaPenyerahan' => 0.0, 'volume' => null])]);

        $this->assertSame(0.0, $p['bruto']);
        $this->assertSame(0.0, $p['netto']);
        $this->assertSame(0.0, $p['hargaPenyerahan']);
        $this->assertNull($p['barang'][0]['netto']);
        $this->assertNull($p['barang'][0]['jumlahKemasan']);
        $this->assertNull($p['barang'][0]['volume']);
        $this->assertSame(0, $p['barang'][0]['barangTarif'][0]['jumlahSatuan']);
    }

    public function test_encode_keeps_zero_fractions_and_nulls(): void
    {
        $json = (new Bc40PayloadBuilder)->encode($this->build());

        $this->assertStringStartsWith('{"asalData":"S","asuransi":0.0,"bruto":520.0,"kodeJenisTpb":"1","hargaPenyerahan":15000000.0,', $json);
        $this->assertStringNotContainsString('isiPer', $json);
        $this->assertStringContainsString('"vd":0.0', $json);
        $this->assertStringContainsString('"kontainer":[]}', $json);
        $this->assertStringContainsString('"nilaiPungutan":0}', $json);
    }
}
