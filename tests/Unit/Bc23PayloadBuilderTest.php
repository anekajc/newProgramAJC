<?php

namespace Tests\Unit;

use App\Services\BeaCukai\Bc23PayloadBuilder;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

// Payload CEISA BC 2.3 mengikuti Villiger (urutan key + tipe), lihat docs/documentbc23_plan.md §9-§10, dengan beda
// yang disepakati setelah dicek ke skema resmi docs/bc_schema/bc23.json: tanggalTiba sesuai isian (Q-a),
// jumlahKontainer = jumlah peti kemas, valuta ke kode ISO, nilai root 2 desimal, entitas slot tetap dan key barang
// di luar skema (nilaiDiskon, volume, nilaiJasa, persen*) tidak dikirim.
class Bc23PayloadBuilderTest extends TestCase
{
    private const ROOT_KEYS = [
        'asalData', 'asuransi', 'bruto', 'cif', 'fob', 'freight', 'hargaPenyerahan', 'jabatanTtd', 'jumlahKontainer',
        'kodeAsuransi', 'kodeDokumen', 'kodeIncoterm', 'kodeKantor', 'kodeKantorBongkar', 'kodePelBongkar', 'kodePelMuat',
        'kodePelTransit', 'kodeTps', 'kodeTujuanTpb', 'kodeTutupPu', 'kodeValuta', 'kotaTtd', 'namaTtd', 'ndpbm', 'netto',
        'nik', 'nilaiBarang', 'nomorAju', 'nomorBc11', 'posBc11', 'seri', 'subposBc11', 'tanggalBc11', 'tanggalTiba',
        'tanggalTtd', 'biayaTambahan', 'biayaPengurang', 'kodeKenaPajak', 'barang', 'entitas', 'kemasan', 'kontainer',
        'dokumen', 'pengangkut',
    ];

    private const BARANG_KEYS = [
        'idBarang', 'asuransi', 'cif', 'diskon', 'fob', 'freight', 'hargaEkspor', 'hargaPenyerahan', 'hargaSatuan',
        'isiPerKemasan', 'jumlahKemasan', 'jumlahSatuan', 'kodeBarang', 'kodeDokumen', 'kodeKategoriBarang',
        'kodeJenisKemasan', 'kodeNegaraAsal', 'kodePerhitungan', 'kodeSatuanBarang', 'merk', 'netto', 'nilaiBarang',
        'nilaiTambah', 'posTarif', 'seriBarang', 'spesifikasiLain', 'tipe', 'ukuran', 'uraian', 'ndpbm', 'cifRupiah',
        'hargaPerolehan', 'kodeAsalBahanBaku', 'barangTarif', 'barangDokumen',
    ];

    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '00002300000020261027000001',
            'kodeKantor' => '071100',
            'kodeKantorBongkar' => '070100',
            'kodeTujuanTpb' => '1',
            'nomorBc11' => '001234',
            'tanggalBc11' => '2026-10-20',
            'posBc11' => '0001',
            'subPosBc11' => '0002',
            'kodePelMuat' => 'MYPKG',
            'kodePelTransit' => '',
            'kodePelBongkar' => 'IDSUB',
            'kodeTps' => 'TPS01',
            'tanggalTiba' => '2026-10-25',
            'kodeValuta' => 'MYR',
            'ndpbm' => 3750.0,
            'kodeIncoterm' => 'CIF',
            'fob' => 17000.9,
            'freight' => 1000.0,
            'asuransi' => 0.0,
            'kodeAsuransi' => 'LN',
            'cif' => 18000.0,
            'bruto' => 1250.5,
            'netto' => 1200.123456,
            'kodeCaraAngkut' => '1',
            'namaPengangkut' => 'MAERSK X',
            'nomorPengangkut' => 'V123',
            'kodeBendera' => 'MY',
            'jumlahKemasan' => 0,
            'kodeJenisKemasan' => '',
            'merkKemasan' => '',
            'nomorKontainer' => null,
            'kodeUkuranKontainer' => '',
            'kodeTipeKontainer' => '',
            'kodeJenisKontainer' => '',
            'kotaTtd' => 'Jember',
            'tanggalTtd' => '2026-10-27',
            'namaTtd' => 'Budi',
            'jabatanTtd' => 'Manager',
        ], $override);
    }

    private function barang(int $seri, array $override = []): array
    {
        return array_merge([
            'seriBarang' => $seri,
            'posTarif' => '84879000',
            'kodeBarang' => 'SP-00'.$seri,
            'uraian' => 'BEARING',
            'merk' => null,
            'tipe' => null,
            'ukuran' => null,
            'spesifikasiLain' => null,
            'kodeSatuanBarang' => 'PCE',
            'jumlahSatuan' => 10.0,
            'jumlahKemasan' => 0.0,
            'kodeJenisKemasan' => '',
            'netto' => 100.4567,
            'cif' => 1500.0,
            'cifRupiah' => 5625000.0,
            'ndpbm' => 3750.0,
            'fob' => 17000.0,
            'asuransi' => 0.0,
            'freight' => 1000.0,
            'kodeNegaraAsal' => '',
            'kodeKategoriBarang' => '',
            'persenBM' => 5.0,
            'persenPPN' => 11.0,
            'persenPPh' => 2.5,
        ], $override);
    }

    private function dokumen(int $seri, string $kode, ?string $nomor = 'INV-1', ?string $tgl = '2026-10-01'): array
    {
        return ['seriDokumen' => $seri, 'kodeDokumen' => $kode, 'nomorDokumen' => $nomor, 'tanggalDokumen' => $tgl];
    }

    private function entitas(): array
    {
        return [
            ['namaEntitas' => 'PT IMPORTIR', 'alamatEntitas' => 'Jember', 'nomorIdentitas' => '01.234', 'nibEntitas' => 'NIB1',
                'nomorIjinEntitas' => 'IZIN-1', 'tanggalIjinEntitas' => '2020-01-02', 'kodeJenisIdentitas' => '6', 'kodeJenisApi' => '02',
                'kodeStatus' => '0', 'kodeNegara' => 'ID'],
            ['namaEntitas' => 'PRESTAR SDN BHD', 'alamatEntitas' => 'Penang', 'nomorIdentitas' => '', 'kodeNegara' => 'MY'],
            ['namaEntitas' => 'PT PEMILIK', 'alamatEntitas' => 'Surabaya', 'nomorIdentitas' => '09.876', 'kodeJenisApi' => null,
                'kodeJenisIdentitas' => null, 'kodeStatus' => null, 'nomorIjinEntitas' => null, 'tanggalIjinEntitas' => null],
        ];
    }

    private function build(array $header = [], array $barang = [], array $dokumen = []): array
    {
        return (new Bc23PayloadBuilder)->build(
            $this->header($header),
            ['barang' => $barang ?: [$this->barang(1)], 'dokumen' => $dokumen ?: [$this->dokumen(1, '380')]],
            $this->entitas()
        );
    }

    public function test_root_key_order(): void
    {
        $this->assertSame(self::ROOT_KEYS, array_keys($this->build()));
    }

    public function test_root_values_and_types(): void
    {
        $p = $this->build();

        $this->assertSame('S', $p['asalData']);
        $this->assertSame('23', $p['kodeDokumen']);
        $this->assertSame('11', $p['kodeTutupPu']);
        $this->assertSame('1', $p['kodeKenaPajak']);
        $this->assertSame('0', $p['nik']);
        $this->assertSame(1, $p['seri']);
        $this->assertSame(0, $p['jumlahKontainer']);
        $this->assertSame(0.0, $p['hargaPenyerahan']);
        $this->assertSame(0.0, $p['biayaTambahan']);
        $this->assertSame(0.0, $p['biayaPengurang']);
        $this->assertSame(18000.0, $p['nilaiBarang']);
        $this->assertSame(0.0, $p['asuransi']); // float walau 0
        $this->assertSame(1200.123456, $p['netto']); // tidak dibulatkan
        $this->assertSame('0002', $p['subposBc11']);
        $this->assertSame('2026-10-25', $p['tanggalTiba']);
    }

    // Q-a: tanggal tiba tetap "2026-06-02" milik Villiger tidak dipakai lagi.
    public function test_tanggal_tiba_not_hardcoded(): void
    {
        $this->assertSame('', $this->build(['tanggalTiba' => null])['tanggalTiba']);
    }

    // Q-r: kolom header tanpa fallback dikirim null.
    public function test_header_text_without_fallback_stays_null(): void
    {
        $p = $this->build(['nomorBc11' => null, 'posBc11' => null, 'subPosBc11' => null, 'namaTtd' => null]);

        $this->assertNull($p['nomorBc11']);
        $this->assertNull($p['posBc11']);
        $this->assertNull($p['subposBc11']);
        $this->assertNull($p['namaTtd']);
        $this->assertSame('', $p['kodePelTransit']);
    }

    public function test_entitas_slots_and_key_order(): void
    {
        $e = $this->build()['entitas'];

        $this->assertCount(3, $e);
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'namaEntitas', 'nibEntitas', 'nomorIdentitas',
            'nomorIjinEntitas', 'tanggalIjinEntitas', 'seriEntitas'], array_keys($e[0]));
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeNegara', 'namaEntitas', 'seriEntitas'], array_keys($e[1]));
        // Pemilik Barang tanpa izin: nomor / tanggal izin tidak dikirim (opsional di skema, "" bukan tanggal valid).
        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas',
            'nomorIdentitas', 'seriEntitas'], array_keys($e[2]));

        $this->assertSame(['3', '5', '7'], array_column($e, 'kodeEntitas'));
        $this->assertSame([3, 5, 7], array_column($e, 'seriEntitas'));
        $this->assertSame('MY', $e[1]['kodeNegara']);
        $this->assertSame('02', $e[2]['kodeJenisApi']);
        $this->assertSame('6', $e[2]['kodeJenisIdentitas']);
        $this->assertSame('0', $e[2]['kodeStatus']);
        $this->assertSame('2020-01-02', $e[0]['tanggalIjinEntitas']);
    }

    // Entitas slot tetap: E1->3, E2->5, E3->7; slot kosong tidak menggeser yang lain.
    public function test_entitas_fixed_slots_do_not_shift(): void
    {
        $entitas = $this->entitas();
        $e = (new Bc23PayloadBuilder)->build($this->header(), ['barang' => [$this->barang(1)]], [null, $entitas[1], $entitas[2]])['entitas'];

        $this->assertSame('', $e[0]['namaEntitas']); // pengusaha kosong tetap di seri 3
        $this->assertSame('6', $e[0]['kodeJenisIdentitas']);
        $this->assertSame('PRESTAR SDN BHD', $e[1]['namaEntitas']);
        $this->assertSame('PT PEMILIK', $e[2]['namaEntitas']);
        $this->assertSame([3, 5, 7], array_column($e, 'seriEntitas'));
    }

    public function test_pemilik_izin_sent_only_when_filled(): void
    {
        $entitas = $this->entitas();
        $entitas[2]['nomorIjinEntitas'] = 'IZIN-9';
        $entitas[2]['tanggalIjinEntitas'] = '2021-03-04';
        $e = (new Bc23PayloadBuilder)->build($this->header(), ['barang' => [$this->barang(1)]], $entitas)['entitas'][2];

        $this->assertSame(['alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas',
            'nomorIdentitas', 'nomorIjinEntitas', 'tanggalIjinEntitas', 'seriEntitas'], array_keys($e));
        $this->assertSame('2021-03-04', $e['tanggalIjinEntitas']);
    }

    public function test_barang_key_order_and_values(): void
    {
        $b = $this->build()['barang'][0];

        $this->assertSame(self::BARANG_KEYS, array_keys($b));
        $this->assertSame('0', $b['idBarang']);
        $this->assertSame('23', $b['kodeDokumen']);
        $this->assertSame(17000, $b['fob']); // bilangan bulat
        $this->assertSame(1000.0, $b['freight']);
        $this->assertSame(0.0, $b['asuransi']);
        $this->assertSame(1500.0, $b['nilaiBarang']);
        $this->assertSame(1500.0, $b['nilaiTambah']);
        $this->assertSame('-', $b['merk']);
        $this->assertSame('0', $b['tipe']);
        $this->assertSame('0', $b['ukuran']);
        $this->assertSame('0', $b['spesifikasiLain']);
        $this->assertSame(100.4567, $b['netto']); // tidak dibulatkan
        $this->assertSame(5625000.0, $b['cifRupiah']);
        foreach (['volume', 'nilaiJasa', 'nilaiDiskon', 'persenBM', 'persenPPN', 'persenPPh'] as $diluarSkema) {
            $this->assertArrayNotHasKey($diluarSkema, $b);
        }
        $this->assertSame([], $b['barangDokumen']);
        $this->assertSame('0', $b['kodeAsalBahanBaku']);
        $this->assertSame('0', $b['kodePerhitungan']);
    }

    public function test_barang_tarif_order_and_values(): void
    {
        $t = $this->build()['barang'][0]['barangTarif'];

        $this->assertCount(3, $t);
        $this->assertSame(['kodeJenisTarif', 'jumlahSatuan', 'kodeFasilitasTarif', 'kodeJenisPungutan', 'kodeSatuanBarang',
            'nilaiBayar', 'nilaiFasilitas', 'nilaiSudahDilunasi', 'seriBarang', 'tarif', 'tarifFasilitas'], array_keys($t[0]));
        foreach ([1, 2] as $i) {
            $this->assertSame(['kodeJenisTarif', 'jumlahSatuan', 'kodeFasilitasTarif', 'kodeSatuanBarang', 'kodeJenisPungutan',
                'nilaiBayar', 'nilaiFasilitas', 'nilaiSudahDilunasi', 'seriBarang', 'tarif', 'tarifFasilitas'], array_keys($t[$i]));
        }
        $this->assertSame(['BM', 'PPH', 'PPN'], array_column($t, 'kodeJenisPungutan'));
        $this->assertSame([40, 2.5, 11], array_column($t, 'tarif'));
        $this->assertSame(['3', '6', '6'], array_column($t, 'kodeFasilitasTarif'));
        // jumlahSatuan = netto TANPA pembulatan di ketiganya (beda dengan BC 2.6.1)
        $this->assertSame([100.4567, 100.4567, 100.4567], array_column($t, 'jumlahSatuan'));
        $this->assertSame([5625000.0, 5625000.0, 5625000.0], array_column($t, 'nilaiBayar'));
    }

    public function test_barang_sorted_by_seri_and_lists_reindexed(): void
    {
        $p = $this->build([], [$this->barang(2, ['kodeBarang' => 'B']), $this->barang(1, ['kodeBarang' => 'A'])]);

        $this->assertSame(['A', 'B'], array_column($p['barang'], 'kodeBarang'));
        $this->assertSame('[', substr(json_encode($p['barang']), 0, 1));
        $this->assertSame('[', substr(json_encode($p['dokumen']), 0, 1));
    }

    public function test_dokumen_fields_and_null_date(): void
    {
        $d = $this->build([], [], [$this->dokumen(1, '380'), $this->dokumen(2, '705', null, null)])['dokumen'];

        $this->assertSame(['idDokumen', 'kodeDokumen', 'nomorDokumen', 'seriDokumen', 'tanggalDokumen'], array_keys($d[0]));
        $this->assertSame('0', $d[0]['idDokumen']);
        $this->assertSame('2026-10-01', $d[0]['tanggalDokumen']);
        $this->assertNull($d[1]['nomorDokumen']);
        $this->assertNull($d[1]['tanggalDokumen']);
    }

    public function test_pengangkut_always_one(): void
    {
        $p = $this->build(['namaPengangkut' => ''])['pengangkut'];

        $this->assertCount(1, $p);
        $this->assertSame(['kodeBendera', 'namaPengangkut', 'nomorPengangkut', 'kodeCaraAngkut', 'seriPengangkut'], array_keys($p[0]));
        $this->assertSame(0, $p[0]['seriPengangkut']);
        $this->assertSame('', $p[0]['namaPengangkut']);
    }

    public function test_kemasan_only_when_filled(): void
    {
        $this->assertSame([], $this->build()['kemasan']);

        $k = $this->build(['jumlahKemasan' => 20, 'kodeJenisKemasan' => 'BL', 'merkKemasan' => 'ABC'])['kemasan'];
        $this->assertSame([['jumlahKemasan' => 20, 'kodeJenisKemasan' => 'BL', 'seriKemasan' => 1, 'merkKemasan' => 'ABC']], $k);

        // Cukup salah satu (jenis ATAU jumlah) yang terisi.
        $this->assertCount(1, $this->build(['kodeJenisKemasan' => 'BL'])['kemasan']);
        $this->assertCount(1, $this->build(['jumlahKemasan' => 3])['kemasan']);
    }

    public function test_kontainer_only_with_number(): void
    {
        $this->assertSame([], $this->build()['kontainer']);

        $k = $this->build(['nomorKontainer' => 'MSKU1234567', 'kodeUkuranKontainer' => '40', 'kodeTipeKontainer' => '1', 'kodeJenisKontainer' => '8'])['kontainer'];
        $this->assertSame(['seriKontainer', 'kodeTipeKontainer', 'kodeUkuranKontainer', 'nomorKontainer', 'kodeJenisKontainer'], array_keys($k[0]));
        $this->assertSame('40', $k[0]['kodeUkuranKontainer']);
        $this->assertSame(0, $this->build(['nomorKontainer' => 'X', 'kodeUkuranKontainer' => ''])['kontainer'][0]['kodeUkuranKontainer']); // Q-h
        $this->assertSame(1, $this->build(['nomorKontainer' => 'X'])['jumlahKontainer']); // = jumlah peti kemas yang dikirim
        $this->assertSame(0, $this->build()['jumlahKontainer']);
    }

    public function test_string_zero_is_not_empty(): void
    {
        $p = $this->build([], [$this->barang(1, ['merk' => '0', 'tipe' => '0', 'spesifikasiLain' => '0'])]);

        $this->assertSame('0', $p['barang'][0]['merk']); // bukan "-"
        $this->assertSame('0', Bc23PayloadBuilder::orDefault('0', 'x'));
        $this->assertSame('x', Bc23PayloadBuilder::orDefault(0.0, 'x'));
    }

    public function test_empty_numbers_fall_back_but_barang_nulls_stay(): void
    {
        $p = $this->build(['cif' => null, 'ndpbm' => 0, 'netto' => null], [$this->barang(1, ['netto' => null, 'cif' => null, 'cifRupiah' => null])]);

        $this->assertSame(0.0, $p['cif']);
        $this->assertSame(0.0, $p['ndpbm']);
        $this->assertSame(0.0, $p['netto']);
        $this->assertNull($p['barang'][0]['netto']); // tanpa fallback
        $this->assertNull($p['barang'][0]['cif']);
        $this->assertSame(0.0, $p['barang'][0]['cifRupiah']);
        $this->assertSame(0, $p['barang'][0]['nilaiBarang']);
        $this->assertSame(0, $p['barang'][0]['barangTarif'][0]['jumlahSatuan']);
    }

    // Skema resmi: cif / fob / freight / asuransi / nilaiBarang kelipatan 0.01.
    public function test_root_amounts_rounded_to_two_decimals(): void
    {
        $p = $this->build(['cif' => 1234.5678, 'fob' => 1000.125, 'freight' => 50.004, 'asuransi' => 10.006], [$this->barang(1, ['cif' => 1234.5678, 'jumlahKemasan' => 10.123])]);

        $this->assertSame(1234.57, $p['cif']);
        $this->assertSame(1234.57, $p['nilaiBarang']);
        $this->assertSame(1000.13, $p['fob']);
        $this->assertSame(50.0, $p['freight']);
        $this->assertSame(10.01, $p['asuransi']);
        $this->assertSame(1234.57, $p['barang'][0]['nilaiBarang']);
        $this->assertSame(1234.57, $p['barang'][0]['nilaiTambah']);
        $this->assertSame(10.12, $p['barang'][0]['jumlahKemasan']);
    }

    // Kode DBVALAS yang bukan ISO diterjemahkan lewat config beacukai.valuta_iso; kode lain apa adanya.
    public function test_valuta_translated_to_iso(): void
    {
        $container = Container::getInstance();
        $container->instance('config', new Repository(['beacukai' => ['valuta_iso' => ['RM' => 'MYR', 'EURO' => 'EUR']]]));
        try {
            $this->assertSame('MYR', $this->build(['kodeValuta' => 'RM'])['kodeValuta']);
            $this->assertSame('MYR', $this->build(['kodeValuta' => 'rm'])['kodeValuta']);
            $this->assertSame('EUR', $this->build(['kodeValuta' => 'EURO'])['kodeValuta']);
            $this->assertSame('USD', $this->build(['kodeValuta' => 'USD'])['kodeValuta']);
            $this->assertSame('', $this->build(['kodeValuta' => ''])['kodeValuta']);
        } finally {
            $container->forgetInstance('config');
        }
    }

    public function test_encode_keeps_zero_fractions_and_slashes(): void
    {
        $json = (new Bc23PayloadBuilder)->encode($this->build(['bruto' => 120.0, 'kodeTps' => 'A/B']));

        $this->assertStringContainsString('"bruto":120.0', $json);
        $this->assertStringContainsString('"hargaPenyerahan":0.0', $json);
        $this->assertStringContainsString('"kodeTps":"A/B"', $json);
        $this->assertStringContainsString('"fob":17000,', $json);
        $this->assertStringStartsWith('{"asalData":"S","asuransi":0.0,"bruto":120.0,', $json);
    }
}
