<?php

namespace Tests\Unit;

use App\Http\Controllers\DocumentBc\DocumentBc261Controller;
use App\Services\BeaCukai\Bc261PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use App\Services\BeaCukai\CeisaService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

// Payload CEISA BC 2.6.1: urutan key Villiger (docs/documentbc261_plan.md §9) dengan key dan tipe yang disesuaikan
// ke skema resmi docs/bc_schema/bc261.json (docs/bc_schema/bc_update/bc261_update.md). Pemeriksaan skema memakai
// opsi yang sama dengan DocumentBc261Controller::masalahKirim (WAJIB_ISI dibaca dari controller).
class Bc261PayloadBuilderTest extends TestCase
{
    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '00000000000000000000000001',
            'kodeKantor' => '071100',
            'kodeTujuanPengiriman' => '1',
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
            'cif' => 0.0,
            'cifRupiah' => 24000000.0,
            'ndpbm' => 0.0,
            'kodeNegaraAsal' => 'DE',
            'kodeAsalBarang' => '1',
            'persenBM' => 0.0,
            'persenPPN' => 0.0,
            'persenPPh' => 0.0,
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
            'kodeDokumen' => '23',
            'kodeKantorAsal' => '071100',
            'nomorDaftarDokAsal' => '123456',
            'tanggalDaftarDokAsal' => '2026-01-02',
            'cif' => 0.0,
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
        return (new Bc261PayloadBuilder)->build($header, $children, $entitas);
    }


    private function opsi(): array
    {
        return [
            'bagian' => [
                'barang' => 'Barang', 'bahanBaku' => 'Bahan Baku', 'dokumen' => 'Dokumen',
                'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Penerima Barang'], 'jaminan' => 'Jaminan',
                'kemasan' => 'Kemasan', 'kontainer' => 'Peti Kemas', 'pengangkut' => 'Pengangkut', 'pungutan' => 'Pungutan',
            ],
            'wajibIsi' => (new ReflectionClass(DocumentBc261Controller::class))->getReflectionConstant('WAJIB_ISI')->getValue(),
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer', 'jaminan', 'bahanBaku'],
        ];
    }

    private function lengkap(array $header = []): array
    {
        $header = $this->header($header + ['kodeCaraAngkut' => '3']);
        $children = [
            'barang' => [$this->barang(1)],
            'bahanbaku' => [$this->bahanBaku(1, 1)],
            'kemasan' => [['seriKemasan' => '1', 'jumlahKemasan' => 2, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']],
            'kontainer' => [['seriKontainer' => 1, 'nomorKontainer' => 'ABCU1234567', 'kodeUkuranKontainer' => '20',
                'kodeJenisKontainer' => '8', 'kodeTipeKontainer' => '1']],
            'jaminan' => [['kodeJenisJaminan' => '1', 'nomorJaminan' => 'J1', 'tanggalJaminan' => '2026-10-01', 'nilaiJaminan' => 1000,
                'tanggalJatuhTempo' => '2026-12-01', 'penjamin' => 'Bank', 'nomorBpj' => 'B1', 'tanggalBpj' => '2026-10-01']],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-01']],
        ];

        return $this->build($header, $children, [$this->entitas('A'), $this->entitas('B'), $this->entitas('C')]);
    }

    private function masalah(array $payload): array
    {
        return (new CeisaSchemaValidator)->masalah('bc261', $payload, $this->opsi());
    }

    public function test_root_key_order(): void
    {
        $p = $this->build($this->header());

        $this->assertSame([
            'asalData', 'asuransi', 'biayaTambahan', 'biayaPengurang', 'bruto', 'cif', 'disclaimer',
            'freight', 'hargaPenyerahan', 'jabatanTtd', 'jumlahKontainer', 'kodeDokumen', 'kodeKantor',
            'kodeTujuanPengiriman', 'kodeValuta', 'kotaTtd', 'namaTtd', 'ndpbm', 'netto', 'nik',
            'nilaiBarang', 'nomorAju', 'seri', 'tanggalAju', 'tanggalTtd', 'tempatStuffing',
            'tglAkhirBerlaku', 'tglAwalBerlaku', 'totalDanaSawit', 'barang', 'dokumen', 'entitas',
            'jaminan', 'kemasan', 'kontainer', 'pengangkut', 'pungutan', 'uangMuka', 'vd',
        ], array_keys($p));
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
        $this->assertSame('261', $p['kodeDokumen']);
        $this->assertSame(0, $p['jumlahKontainer']);
    }

    public function test_valuta_is_sent_as_given_when_already_iso(): void
    {
        $this->assertSame('USD', $this->build($this->header())['kodeValuta']);
        $this->assertSame('', $this->build($this->header(['kodeValuta' => '']))['kodeValuta']);
    }

    public function test_empty_document_still_has_fixed_arrays(): void
    {
        $p = $this->build([]);

        $this->assertSame(0.0, $p['bruto']);
        $this->assertSame(0.0, $p['netto']);
        $this->assertSame('', $p['tanggalTtd']);
        $this->assertCount(3, $p['entitas']);
        $this->assertSame([3, 7, 8], array_column($p['entitas'], 'seriEntitas'));
        $this->assertSame(['3', '02', '6', '0'], [
            $p['entitas'][0]['kodeEntitas'], $p['entitas'][0]['kodeJenisApi'],
            $p['entitas'][0]['kodeJenisIdentitas'], $p['entitas'][0]['kodeStatus'],
        ]);
        $this->assertSame([[
            'idPengangkut' => '1', 'kodeCaraAngkut' => '', 'seriPengangkut' => 1,
        ]], $p['pengangkut']);
        $this->assertSame([[
            'idPungutan' => '1', 'kodeFasilitasTarif' => '3', 'kodeJenisPungutan' => 'PPN', 'nilaiPungutan' => 0.0,
        ]], $p['pungutan']);
        $this->assertSame([], $p['barang']);
        $this->assertSame([], $p['kontainer']);
    }

    public function test_entitas_key_order_per_slot(): void
    {
        $p = $this->build($this->header(), [], [$this->entitas('A'), $this->entitas('B'), $this->entitas('C')]);

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
        $this->assertSame(['A', 'B', 'C'], array_column($p['entitas'], 'namaEntitas'));
    }

    public function test_entitas_slots_are_fixed(): void
    {
        // Entitas 1 kosong tidak menggeser entitas 2 dan 3.
        $p = $this->build($this->header(), [], [null, $this->entitas('B'), $this->entitas('C')]);

        $this->assertSame('', $p['entitas'][0]['namaEntitas']);
        $this->assertSame('3', $p['entitas'][0]['kodeEntitas']);
        $this->assertSame('B', $p['entitas'][1]['namaEntitas']);
        $this->assertSame('7', $p['entitas'][1]['kodeEntitas']);
        $this->assertSame('C', $p['entitas'][2]['namaEntitas']);
        $this->assertSame('8', $p['entitas'][2]['kodeEntitas']);
    }

    public function test_string_zero_is_not_empty(): void
    {
        $p = $this->build($this->header(), [
            'barang' => [$this->barang(1, ['merk' => '0', 'tipe' => 'X1'])],
        ], [$this->entitas('A', ['kodeJenisApi' => '0']), null, null]);

        $this->assertSame('0', $p['barang'][0]['merk']);
        $this->assertSame('X1', $p['barang'][0]['tipe']);
        $this->assertSame('0', $p['barang'][0]['ukuran']);
        $this->assertSame('0', $p['entitas'][0]['kodeJenisApi']);
        $this->assertSame('-', $this->build($this->header(), ['barang' => [$this->barang(1)]])['barang'][0]['merk']);
    }

    public function test_barang_keys_and_values(): void
    {
        $p = $this->build($this->header(), ['barang' => [$this->barang(1, ['cifRupiah' => 24000000.125, 'netto' => 100.45678])]]);
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
        $this->assertSame('1', $b['kodeAsalBarang']);
        $this->assertSame(0.0, $b['hargaEkspor']);
        // Pungutan: satu baris PPN senilai jumlah CIF Rupiah semua barang.
        $this->assertSame(24000000.13, $p['pungutan'][0]['nilaiPungutan']);
    }

    public function test_kode_asal_barang_has_no_silent_default(): void
    {
        $b = $this->build($this->header(), ['barang' => [$this->barang(1, ['kodeAsalBarang' => null])]])['barang'][0];

        $this->assertSame('', $b['kodeAsalBarang']);
    }

    public function test_pungutan_sums_all_barang(): void
    {
        $p = $this->build($this->header(), ['barang' => [
            $this->barang(1, ['cifRupiah' => 1000.0]), $this->barang(2, ['cifRupiah' => 2500.505]),
        ]]);

        $this->assertSame(3500.51, $p['pungutan'][0]['nilaiPungutan']);
    }

    public function test_bahan_baku_grouped_per_barang_as_list(): void
    {
        $p = $this->build($this->header(), [
            'barang' => [$this->barang(2), $this->barang(1)],
            'bahanbaku' => [
                $this->bahanBaku(1, 1),
                $this->bahanBaku(2, 1, ['kodeAsalBahanBaku' => '1', 'seriBarangDokAsal' => 3]),
                $this->bahanBaku(2, 2),
            ],
        ]);

        // Barang diurutkan per seriBarang; bahan baku barang 2 tetap list (key 0,1), bukan objek.
        $this->assertSame([1, 2], array_column($p['barang'], 'seriBarang'));
        $this->assertSame([0, 1], array_keys($p['barang'][1]['bahanBaku']));
        $json = (new Bc261PayloadBuilder)->encode($p);
        $this->assertStringContainsString('"bahanBaku":[{', $json);
        $this->assertStringNotContainsString('"bahanBaku":{', $json);

        $bb = $p['barang'][1]['bahanBaku'][0];
        $this->assertSame([
            'seriBarangDokAsal', 'cif', 'cifRupiah', 'flagTis', 'hargaPenyerahan', 'hargaPerolehan',
            'isiPerKemasan', 'jumlahSatuan', 'kodeAsalBahanBaku', 'kodeBarang', 'kodeDokAsal', 'kodeDokumen',
            'kodeKantor', 'kodeSatuanBarang', 'merkBarang', 'ndpbm', 'netto', 'nilaiJasa', 'nomorDaftarDokAsal',
            'nomorDokumen', 'posTarif', 'seriBahanBaku', 'seriBarang', 'seriIjin', 'spesifikasiLainBarang',
            'tanggalDaftarDokAsal', 'tipeBarang', 'ukuranBarang', 'uraianBarang', 'bahanBakuTarif',
        ], array_keys($bb));
        $this->assertSame('1', $bb['kodeAsalBahanBaku']);
        $this->assertSame('23', $bb['kodeDokAsal']);
        $this->assertSame('071100', $bb['kodeKantor']);
        $this->assertSame('123456', $bb['nomorDokumen']);
        $this->assertSame(3, $bb['seriBarangDokAsal']);
        $this->assertSame(1, $p['barang'][1]['bahanBaku'][1]['seriBarangDokAsal']);
        $this->assertSame(1, $bb['seriIjin']);
        $this->assertSame([], $bb['bahanBakuTarif']);
        $this->assertStringContainsString('"bahanBakuTarif":[]', $json);
    }

    public function test_other_collections(): void
    {
        $p = $this->build($this->header(), [
            'kemasan' => [['seriKemasan' => '1', 'jumlahKemasan' => 3, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1', 'tanggalDokumen' => null]],
            'jaminan' => [['kodeJenisJaminan' => '2', 'nilaiJaminan' => 5000000, 'tanggalJaminan' => '2026-10-01']],
            'kontainer' => [['seriKontainer' => 1, 'nomorKontainer' => 'C1', 'kodeUkuranKontainer' => '20', 'kodeJenisKontainer' => '8',
                'kodeTipeKontainer' => '1'], ['seriKontainer' => 2, 'nomorKontainer' => null]],
        ]);

        $this->assertSame([['jumlahKemasan' => 3, 'kodeJenisKemasan' => 'BX', 'seriKemasan' => 1, 'merkKemasan' => '']], $p['kemasan']);
        $this->assertSame([[
            'idDokumen' => '0', 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1', 'seriDokumen' => 1, 'tanggalDokumen' => '',
        ]], $p['dokumen']);
        $this->assertSame([
            'idJaminan', 'kodeJenisJaminan', 'nilaiJaminan', 'nomorBpj', 'nomorJaminan', 'penjamin',
            'tanggalBpj', 'tanggalJaminan', 'tanggalJatuhTempo',
        ], array_keys($p['jaminan'][0]));
        $this->assertSame('2', $p['jaminan'][0]['idJaminan']);
        $this->assertSame(5000000, $p['jaminan'][0]['nilaiJaminan']);
        $this->assertSame('', $p['jaminan'][0]['nomorBpj']);
        $this->assertSame('', $p['jaminan'][0]['tanggalBpj']);
        $this->assertSame([
            'kodeJenisKontainer', 'kodeTipeKontainer', 'kodeUkuranKontainer', 'nomorKontainer', 'seriKontainer',
        ], array_keys($p['kontainer'][0]));
        $this->assertSame(2, $p['jumlahKontainer']);
        $this->assertSame('', $p['kontainer'][1]['nomorKontainer']);
        $this->assertSame([
            'idPengangkut' => '1', 'kodeCaraAngkut' => '3', 'seriPengangkut' => 1,
        ], $p['pengangkut'][0]);
    }

    public function test_complete_document_passes_the_official_schema(): void
    {
        $this->assertSame([], $this->masalah($this->lengkap()));
    }

    public function test_rounded_values_pass_schema_even_with_long_decimals(): void
    {
        $p = $this->lengkap(['cif' => 1500.456, 'netto' => 100.45678, 'bruto' => 120.123456, 'ndpbm' => 16000.123456]);

        $this->assertSame([], $this->masalah($p));
    }

    public function test_incomplete_document_lists_problems(): void
    {
        $header = $this->header(['nomorAju' => 'TEST261', 'kodeKantor' => '', 'kodeCaraAngkut' => '']);
        $p = $this->build($header, [
            'barang' => [$this->barang(1, ['kodeAsalBarang' => null, 'kodeNegaraAsal' => ''])],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => null, 'tanggalDokumen' => null]],
            'jaminan' => [['kodeJenisJaminan' => '1', 'nilaiJaminan' => 10]],
            'kontainer' => [['seriKontainer' => 1, 'nomorKontainer' => '', 'kodeUkuranKontainer' => '20', 'kodeTipeKontainer' => '1', 'kodeJenisKontainer' => '8']],
        ]);

        $semua = implode("\n", $this->masalah($p));

        $this->assertStringContainsString('Nomor AJU (nomorAju): harus tepat 26 karakter huruf/angka', $semua);
        $this->assertStringContainsString('Kantor Pabean (kodeKantor): wajib diisi', $semua);
        $this->assertStringContainsString('Barang #1 > Asal Barang (kodeAsalBarang): wajib diisi', $semua);
        $this->assertStringContainsString('Barang #1 > Negara Asal (kodeNegaraAsal)', $semua);
        $this->assertStringContainsString('Dokumen #1 > Nomor (nomorDokumen): wajib diisi', $semua);
        $this->assertStringContainsString('Dokumen #1 > Tanggal (tanggalDokumen): wajib diisi', $semua);
        $this->assertStringContainsString('Jaminan #1 > Nomor Jaminan (nomorJaminan): wajib diisi', $semua);
        $this->assertStringContainsString('Jaminan #1 > Tanggal BPJ (tanggalBpj): wajib diisi', $semua);
        $this->assertStringContainsString('Peti Kemas #1 > Nomor Peti Kemas (nomorKontainer): wajib diisi', $semua);
        $this->assertStringContainsString('Pengangkut #1 > Cara Pengangkutan (kodeCaraAngkut): wajib dipilih', $semua);
        $this->assertStringContainsString('Pengusaha TPB > NIB (nibEntitas)', $semua);
    }

    public function test_cara_angkut_outside_schema_is_reported(): void
    {
        $semua = implode("\n", $this->masalah($this->lengkap(['kodeCaraAngkut' => '10'])));

        $this->assertStringContainsString('Cara Pengangkutan (kodeCaraAngkut): harus salah satu dari: 1, 2, 3, 4, 5, 6, 7, 8, 9', $semua);
    }

    public function test_encode_keeps_zero_fraction_and_slashes(): void
    {
        $builder = new Bc261PayloadBuilder;
        $json = $builder->encode($builder->build($this->header(), [
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1']],
        ], [null, null, null]));

        $this->assertStringStartsWith('{"asalData":"S","asuransi":0,"biayaTambahan":0,"biayaPengurang":0,"bruto":120.0,"cif":1500.0,', $json);
        $this->assertStringContainsString('"INV/1"', $json);
    }

    public function test_ceisa_details(): void
    {
        $this->assertSame(['Nomor aju sudah ada'], CeisaService::details(json_encode([
            'message' => ['$Nomor aju sudah ada$ [CODE:E01]', 'Lihat referensi kode error di ...'],
        ])));
        $this->assertSame(['Kode kantor salah'], CeisaService::details(json_encode(['message' => '$Kode kantor salah [CODE:K9]'])));

        $banyak = CeisaService::details(json_encode(['message' => ['a', 'b', 'c', 'd', 'e', 'f', 'g']]));
        $this->assertSame(['a', 'b', 'c', 'd', 'e', '+2 lagi'], $banyak);

        $this->assertSame(['<html>gateway error</html>...'], CeisaService::details('<html>gateway error</html>'));
        $this->assertSame(['{"status":"FAILED"}'], CeisaService::details('{"status":"FAILED"}'));
    }
}
