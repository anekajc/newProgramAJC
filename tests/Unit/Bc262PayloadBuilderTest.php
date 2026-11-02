<?php

namespace Tests\Unit;

use App\Http\Controllers\DocumentBc\DocumentBc262Controller;
use App\Services\BeaCukai\Bc262PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

// Payload CEISA BC 2.6.2 sesuai skema resmi docs/bc_schema/bc262.json (docs/bc_schema/bc_update/bc262_update.md).
// Pemeriksaan sebelum kirim memakai DocumentBc262Controller::masalahKirim apa adanya (WAJIB_ISI dan aturan tambahan).
class Bc262PayloadBuilderTest extends TestCase
{
    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '00000000000000000000000001',
            'kodeKantor' => '071100',
            'kodeTujuanPemasukan' => '1',
            'kodeValuta' => 'USD',
            'ndpbm' => 16000.0,
            'cif' => 1500.0,
            'bruto' => 120.0,
            'netto' => 100.456,
            'kodeCaraAngkut' => '3',
            'kotaTtd' => 'Jember',
            'tanggalTtd' => '2026-10-26',
            'namaTtd' => 'Budi',
            'jabatanTtd' => 'Manager',
            'nomorAjuSumber' => '00026110000012026102900001',
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
            'jumlahKemasan' => 1.0,
            'kodeJenisKemasan' => 'BX',
            'netto' => 100.5,
            'cif' => 1500.0,
            'cifRupiah' => 24000000.0,
            'ndpbm' => 16000.0,
            'kodeNegaraAsal' => 'DE',
            'kodeAsalBarang' => '1',
        ], $override);
    }

    private function bahanBaku(int $seriBarang, int $seri, array $override = []): array
    {
        return array_merge([
            'seriBarang' => $seriBarang,
            'seriBahanBaku' => $seri,
            'kodeAsalBahanBaku' => '0',
            'posTarif' => '40169390',
            'kodeBarang' => 'BB-'.$seri,
            'uraianBarang' => 'SEAL',
            'kodeSatuanBarang' => 'PCE',
            'jumlahSatuan' => 2.0,
            'netto' => 1.23456,
            'kodeDokumen' => '23',
            'kodeKantorAsal' => '071100',
            'nomorDaftarDokAsal' => '123456',
            'tanggalDaftarDokAsal' => '2026-01-02',
            'cif' => 10.0,
            'cifRupiah' => 50000.125,
        ], $override);
    }

    private function entitas(string $nama, array $override = []): array
    {
        return array_merge([
            'namaEntitas' => $nama,
            'alamatEntitas' => 'Jl. '.$nama,
            'nomorIdentitas' => '0123456789012345',
            'nibEntitas' => '9120000000000',
            'nomorIjinEntitas' => 'KEP-1',
            'tanggalIjinEntitas' => '2020-05-01',
            'kodeJenisApi' => null,
            'kodeJenisIdentitas' => null,
            'kodeStatus' => null,
            'kodeNegara' => 'ID',
        ], $override);
    }

    private function build(array $header, array $children = [], array $entitas = [null, null, null]): array
    {
        return (new Bc262PayloadBuilder)->build($header, $children, $entitas);
    }

    private function lengkap(array $header = []): array
    {
        $children = [
            'barang' => [$this->barang(1)],
            'bahanbaku' => [$this->bahanBaku(1, 1)],
            'kemasan' => [['seriKemasan' => 1, 'jumlahKemasan' => 2, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']],
            'kontainer' => [['seriKontainer' => 1, 'nomorKontainer' => 'ABCU1234567', 'kodeUkuranKontainer' => '20',
                'kodeJenisKontainer' => '8', 'kodeTipeKontainer' => '1']],
            'jaminan' => [['kodeJenisJaminan' => '1', 'nomorJaminan' => 'J1', 'tanggalJaminan' => '2026-10-01', 'nilaiJaminan' => 1000,
                'tanggalJatuhTempo' => '2026-12-01', 'penjamin' => 'Bank', 'nomorBpj' => 'B1', 'tanggalBpj' => '2026-10-01']],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-01']],
            'pungutan' => [
                ['seriPungutan' => 1, 'kodeJenisPungutan' => 'BM', 'kodeFasilitasTarif' => '1', 'nilaiPungutan' => 24000000.0],
                ['seriPungutan' => 2, 'kodeJenisPungutan' => 'PPN', 'kodeFasilitasTarif' => '5', 'nilaiPungutan' => 100.505],
            ],
        ];

        return $this->build($this->header($header), $children, [$this->entitas('A'), $this->entitas('B'), $this->entitas('C')]);
    }

    // Pemeriksaan kirim yang sama dengan yang dipakai controller (masalahKirim private).
    private function masalah(array $payload): array
    {
        $kelas = new ReflectionClass(DocumentBc262Controller::class);
        $controller = $kelas->newInstanceWithoutConstructor();
        $kelas->getProperty('skema')->setValue($controller, new CeisaSchemaValidator);

        return $kelas->getMethod('masalahKirim')->invoke($controller, $payload);
    }

    public function test_root_key_order(): void
    {
        $this->assertSame([
            'asalData', 'asuransi', 'biayaTambahan', 'biayaPengurang', 'bruto', 'cif', 'disclaimer',
            'freight', 'hargaPenyerahan', 'jabatanTtd', 'kodeDokumen', 'kodeKantor',
            'kodeTujuanPemasukan', 'kodeValuta', 'kotaTtd', 'namaTtd', 'ndpbm', 'netto', 'nik',
            'nilaiBarang', 'nomorAju', 'seri', 'tanggalAju', 'tanggalTtd', 'barang', 'dokumen', 'entitas',
            'jaminan', 'kemasan', 'kontainer', 'pengangkut', 'pungutan', 'uangMuka', 'vd',
        ], array_keys($this->build($this->header())));
    }

    public function test_root_values_and_rounding(): void
    {
        $p = $this->build($this->header(['cif' => 1500.456, 'netto' => 100.4567, 'bruto' => 120.12345, 'ndpbm' => 16000.12345]));

        $this->assertSame(1500.46, $p['cif']);
        $this->assertSame(1500.46, $p['hargaPenyerahan']);
        $this->assertSame(1500.46, $p['nilaiBarang']);
        $this->assertSame(100.4567, $p['netto']);
        $this->assertSame(120.1235, $p['bruto']);
        $this->assertSame(16000.1235, $p['ndpbm']);
        $this->assertSame('2026-10-26', $p['tanggalAju']);
        $this->assertSame('262', $p['kodeDokumen']);
        $this->assertSame('1', $p['kodeTujuanPemasukan']);
        $this->assertArrayNotHasKey('jumlahKontainer', $p);
        $this->assertArrayNotHasKey('tempatStuffing', $p);
    }

    public function test_valuta_is_translated_to_iso(): void
    {
        $this->assertSame('USD', $this->build($this->header())['kodeValuta']);
        $this->assertSame('', $this->build($this->header(['kodeValuta' => '']))['kodeValuta']);
    }

    public function test_empty_document_still_has_fixed_arrays(): void
    {
        $p = $this->build([]);

        $this->assertSame(0.0, $p['bruto']);
        $this->assertSame('', $p['tanggalTtd']);
        $this->assertSame([3, 7, 9], array_column($p['entitas'], 'seriEntitas'));
        $this->assertSame(['3', '7', '9'], array_column($p['entitas'], 'kodeEntitas'));
        $this->assertSame([['idPengangkut' => '1', 'kodeCaraAngkut' => '', 'seriPengangkut' => 1]], $p['pengangkut']);
        $this->assertSame([], $p['barang']);
        $this->assertSame([], $p['kontainer']);
        $this->assertSame([], $p['pungutan']);
    }

    public function test_entitas_key_order_per_slot_and_fixed_slots(): void
    {
        $p = $this->build($this->header(), [], [null, $this->entitas('B'), $this->entitas('C')]);

        $this->assertSame([
            'alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'namaEntitas', 'kodeStatus',
            'nibEntitas', 'nomorIdentitas', 'nomorIjinEntitas', 'tanggalIjinEntitas', 'seriEntitas',
        ], array_keys($p['entitas'][0]));
        $this->assertSame([
            'alamatEntitas', 'kodeEntitas', 'nomorIdentitas', 'kodeStatus', 'namaEntitas', 'kodeJenisIdentitas', 'seriEntitas',
        ], array_keys($p['entitas'][1]));
        $this->assertSame([
            'alamatEntitas', 'kodeEntitas', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas', 'nomorIdentitas', 'seriEntitas',
        ], array_keys($p['entitas'][2]));
        // Entitas 1 kosong tidak menggeser entitas 2 dan 3.
        $this->assertSame(['', 'B', 'C'], array_column($p['entitas'], 'namaEntitas'));
        $this->assertSame(['3', '7', '9'], array_column($p['entitas'], 'kodeEntitas'));
    }

    public function test_barang_keys_and_values(): void
    {
        $p = $this->build($this->header(), ['barang' => [$this->barang(1, ['cifRupiah' => 24000000.125, 'netto' => 100.45678, 'jumlahKemasan' => 1.005])]]);
        $b = $p['barang'][0];

        $this->assertSame([
            'cif', 'cifRupiah', 'hargaEkspor', 'hargaPenyerahan', 'isiPerKemasan', 'jumlahKemasan',
            'jumlahSatuan', 'kodeAsalBahanBaku', 'kodeAsalBarang', 'kodeBarang', 'kodeDokumen', 'kodeJenisKemasan',
            'kodeNegaraAsal', 'kodeSatuanBarang', 'merk', 'ndpbm', 'netto', 'nilaiBarang', 'nilaiJasa', 'posTarif',
            'seriBarang', 'spesifikasiLain', 'hargaPerolehan', 'tipe', 'uangMuka', 'ukuran', 'uraian', 'bahanBaku',
        ], array_keys($b));

        $this->assertSame(24000000.13, $b['cifRupiah']);
        $this->assertSame(24000000.13, $b['nilaiBarang']);
        $this->assertSame(100.4568, $b['netto']);
        $this->assertSame(1.01, $b['jumlahKemasan']);
        $this->assertSame(1500.0, $b['cif']);
        $this->assertSame('262', $b['kodeDokumen']);
        $this->assertSame('-', $b['merk']);
        $this->assertSame('0', $b['tipe']);
        $this->assertSame('1', $b['kodeAsalBarang']);
    }

    public function test_kode_asal_barang_and_jenis_kemasan_have_no_silent_default(): void
    {
        $b = $this->build($this->header(), ['barang' => [$this->barang(1, ['kodeAsalBarang' => null, 'kodeJenisKemasan' => null])]])['barang'][0];

        $this->assertSame('', $b['kodeAsalBarang']);
        $this->assertSame('', $b['kodeJenisKemasan']);
    }

    public function test_bahan_baku_keys_values_and_source_nomor_aju(): void
    {
        $p = $this->build($this->header(), [
            'barang' => [$this->barang(2), $this->barang(1)],
            'bahanbaku' => [
                $this->bahanBaku(1, 1),
                $this->bahanBaku(2, 1, ['kodeAsalBahanBaku' => '1', 'seriBarangDokAsal' => 3]),
                $this->bahanBaku(2, 2),
            ],
        ]);

        // Barang diurutkan per seriBarang; bahan baku tetap list.
        $this->assertSame([1, 2], array_column($p['barang'], 'seriBarang'));
        $json = (new Bc262PayloadBuilder)->encode($p);
        $this->assertStringContainsString('"bahanBaku":[{', $json);

        $bb = $p['barang'][1]['bahanBaku'][0];
        $this->assertSame([
            'cif', 'cifRupiah', 'hargaPenyerahan', 'hargaPerolehan', 'jumlahSatuan', 'kodeSatuanBarang', 'kodeAsalBahanBaku',
            'kodeBarang', 'kodeDokAsal', 'kodeDokumen', 'kodeKantor', 'merkBarang', 'ndpbm', 'netto', 'nomorAjuDokAsal',
            'nomorDaftarDokAsal', 'posTarif', 'seriBahanBaku', 'seriBarang', 'seriBarangDokAsal', 'seriIjin',
            'spesifikasiLainBarang', 'tanggalDaftarDokAsal', 'tipeBarang', 'ukuranBarang', 'uraianBarang', 'nilaiJasa', 'bahanBakuTarif',
        ], array_keys($bb));
        $this->assertSame('1', $bb['kodeAsalBahanBaku']);
        $this->assertSame('23', $bb['kodeDokAsal']);
        $this->assertSame('262', $bb['kodeDokumen']);
        $this->assertSame('071100', $bb['kodeKantor']);
        $this->assertSame(1.2346, $bb['netto']);
        $this->assertSame(3, $bb['seriBarangDokAsal']);
        $this->assertSame(1, $p['barang'][1]['bahanBaku'][1]['seriBarangDokAsal']);
        // Nomor AJU dokumen asal = nomor AJU BC 2.6.1 sumber; bahanBakuTarif kosong.
        $this->assertSame('00026110000012026102900001', $bb['nomorAjuDokAsal']);
        $this->assertSame([], $bb['bahanBakuTarif']);
        $this->assertStringContainsString('"bahanBakuTarif":[]', $json);

        $tanpaSumber = $this->build($this->header(['nomorAjuSumber' => '']), ['barang' => [$this->barang(1)], 'bahanbaku' => [$this->bahanBaku(1, 1)]]);
        $this->assertSame('', $tanpaSumber['barang'][0]['bahanBaku'][0]['nomorAjuDokAsal']);
    }

    public function test_pungutan_follows_the_rows(): void
    {
        $p = $this->build($this->header(), ['pungutan' => [
            ['seriPungutan' => 1, 'kodeJenisPungutan' => 'BM', 'kodeFasilitasTarif' => '1', 'nilaiPungutan' => 24000000.0],
            ['seriPungutan' => 2, 'kodeJenisPungutan' => 'PPN', 'kodeFasilitasTarif' => '5', 'nilaiPungutan' => 2500.505],
        ]]);

        $this->assertSame([
            ['idPungutan' => '1', 'kodeFasilitasTarif' => '1', 'kodeJenisPungutan' => 'BM', 'nilaiPungutan' => 24000000.0],
            ['idPungutan' => '2', 'kodeFasilitasTarif' => '5', 'kodeJenisPungutan' => 'PPN', 'nilaiPungutan' => 2500.51],
        ], $p['pungutan']);
        $this->assertSame([], $this->build($this->header())['pungutan']);
    }

    public function test_other_collections(): void
    {
        $p = $this->build($this->header(), [
            'kemasan' => [['seriKemasan' => 1, 'jumlahKemasan' => 3, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1', 'tanggalDokumen' => null]],
            'jaminan' => [['kodeJenisJaminan' => '2', 'nilaiJaminan' => 5000000, 'tanggalJaminan' => '2026-10-01']],
            'kontainer' => [['seriKontainer' => 1, 'nomorKontainer' => 'C1', 'kodeUkuranKontainer' => '20', 'kodeJenisKontainer' => '8',
                'kodeTipeKontainer' => '1'], ['seriKontainer' => 2, 'nomorKontainer' => null]],
        ]);

        $this->assertSame([['jumlahKemasan' => 3, 'kodeJenisKemasan' => 'BX', 'seriKemasan' => 1, 'merkKemasan' => '']], $p['kemasan']);
        $this->assertSame([[
            'idDokumen' => '0', 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1', 'seriDokumen' => 1, 'tanggalDokumen' => '',
        ]], $p['dokumen']);
        $this->assertSame('2', $p['jaminan'][0]['idJaminan']);
        $this->assertSame(5000000, $p['jaminan'][0]['nilaiJaminan']);
        $this->assertSame('', $p['jaminan'][0]['nomorBpj']);
        $this->assertSame([
            'kodeJenisKontainer', 'kodeTipeKontainer', 'kodeUkuranKontainer', 'nomorKontainer', 'seriKontainer',
        ], array_keys($p['kontainer'][0]));
        $this->assertSame('', $p['kontainer'][1]['nomorKontainer']);
        $this->assertSame(['idPengangkut' => '1', 'kodeCaraAngkut' => '3', 'seriPengangkut' => 1], $p['pengangkut'][0]);
    }

    public function test_complete_document_passes_the_official_schema(): void
    {
        $this->assertSame([], $this->masalah($this->lengkap()));
    }

    public function test_rounded_values_pass_schema_even_with_long_decimals(): void
    {
        $this->assertSame([], $this->masalah($this->lengkap(['cif' => 1500.456, 'netto' => 100.45678, 'bruto' => 120.123456, 'ndpbm' => 16000.123456])));
    }

    public function test_document_without_pungutan_rows_is_allowed(): void
    {
        $p = $this->lengkap();
        $p['pungutan'] = [];

        $this->assertSame([], $this->masalah($p));
    }

    public function test_incomplete_document_lists_problems(): void
    {
        $header = $this->header(['nomorAju' => 'TEST262', 'kodeKantor' => '', 'kodeCaraAngkut' => '', 'kodeTujuanPemasukan' => '']);
        $p = $this->build($header, [
            'barang' => [$this->barang(1, ['kodeAsalBarang' => null, 'kodeNegaraAsal' => '', 'kodeJenisKemasan' => null])],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => null, 'tanggalDokumen' => null]],
            'jaminan' => [['kodeJenisJaminan' => '1', 'nilaiJaminan' => 10]],
            'kontainer' => [['seriKontainer' => 1, 'nomorKontainer' => '', 'kodeUkuranKontainer' => '20', 'kodeTipeKontainer' => '1', 'kodeJenisKontainer' => '8']],
            'pungutan' => [['seriPungutan' => 1, 'kodeJenisPungutan' => 'BM', 'kodeFasilitasTarif' => '1', 'nilaiPungutan' => 0.0]],
        ]);

        $semua = implode("\n", $this->masalah($p));

        $this->assertStringContainsString('Nomor AJU (nomorAju): harus tepat 26 karakter huruf/angka', $semua);
        $this->assertStringContainsString('Kantor Pabean (kodeKantor): wajib diisi', $semua);
        $this->assertStringContainsString('kodeTujuanPemasukan', $semua);
        $this->assertStringContainsString('Barang #1 > Asal Barang (kodeAsalBarang): wajib diisi', $semua);
        $this->assertStringContainsString('Barang #1 > Negara Asal (kodeNegaraAsal)', $semua);
        $this->assertStringContainsString('Barang #1 > Jenis Kemasan (kodeJenisKemasan): wajib diisi', $semua);
        $this->assertStringContainsString('Dokumen #1 > Nomor (nomorDokumen): wajib diisi', $semua);
        $this->assertStringContainsString('Jaminan #1 > Nomor Jaminan (nomorJaminan): wajib diisi', $semua);
        $this->assertStringContainsString('Peti Kemas #1 > Nomor Peti Kemas (nomorKontainer): wajib diisi', $semua);
        $this->assertStringContainsString('Pengangkut #1 > Cara Pengangkutan (kodeCaraAngkut): wajib dipilih', $semua);
        $this->assertStringContainsString('Kemasan: wajib diisi', $semua);
        $this->assertStringContainsString('Pungutan #1 > Nilai Pungutan (nilaiPungutan): harus lebih dari 0', $semua);
    }

    public function test_standalone_document_reports_missing_source_nomor_aju_once(): void
    {
        $p = $this->build($this->header(['nomorAjuSumber' => '']), [
            'barang' => [$this->barang(1)],
            'bahanbaku' => [$this->bahanBaku(1, 1), $this->bahanBaku(1, 2)],
        ]);

        $semua = $this->masalah($p);
        $kena = array_filter($semua, fn ($m) => str_contains($m, 'nomorAjuDokAsal'));

        $this->assertCount(1, $kena);
        $this->assertStringContainsString('BC 2.6.1 sumber', implode(' ', $kena));
    }

    public function test_many_pungutan_rows_are_all_checked(): void
    {
        $p = $this->lengkap();
        $p['pungutan'][1]['nilaiPungutan'] = 0.0;
        $p['pungutan'][1]['kodeFasilitasTarif'] = '';

        $semua = implode("\n", $this->masalah($p));

        $this->assertStringContainsString('Pungutan #2 > Fasilitas Tarif (kodeFasilitasTarif): wajib diisi', $semua);
        $this->assertStringContainsString('Pungutan #2 > Nilai Pungutan (nilaiPungutan): harus lebih dari 0', $semua);
    }

    public function test_cara_angkut_outside_schema_is_reported(): void
    {
        $semua = implode("\n", $this->masalah($this->lengkap(['kodeCaraAngkut' => '10'])));

        $this->assertStringContainsString('Cara Pengangkutan (kodeCaraAngkut): harus salah satu dari: 1, 2, 3, 4, 5, 6, 7, 8, 9', $semua);
    }

    public function test_encode_keeps_zero_fraction_and_slashes(): void
    {
        $builder = new Bc262PayloadBuilder;
        $json = $builder->encode($builder->build($this->header(), [
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1']],
        ], [null, null, null]));

        $this->assertStringStartsWith('{"asalData":"S","asuransi":0,"biayaTambahan":0,"biayaPengurang":0,"bruto":120.0,"cif":1500.0,', $json);
        $this->assertStringContainsString('"INV/1"', $json);
    }
}
