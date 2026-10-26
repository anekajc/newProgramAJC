<?php

namespace Tests\Unit;

use App\Services\BeaCukai\Bc261PayloadBuilder;
use App\Services\BeaCukai\CeisaService;
use PHPUnit\Framework\TestCase;

// Payload CEISA BC 2.6.1 harus sama persis dengan Villiger (urutan key + tipe), lihat
// docs/documentbc261_plan.md §9-§10.
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
            'kodeKategoriBarang' => null,
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

    public function test_root_key_order(): void
    {
        $p = $this->build($this->header());

        $this->assertSame([
            'asalData', 'asuransi', 'biayaTambahan', 'biayaPengurang', 'bruto', 'cif', 'disclaimer',
            'freight', 'hargaPenyerahan', 'jabatanTtd', 'jumlahKontainer', 'kodeDokumen', 'kodeKantor',
            'kodeTujuanPengiriman', 'kodeValuta', 'kotaTtd', 'namaTtd', 'ndpbm', 'netto', 'nik',
            'nilaiBarang', 'nomorAju', 'seri', 'tanggalAju', 'tanggalTtd', 'tempatStuffing',
            'tglAkhirBerlaku', 'tglAwalBerlaku', 'totalDanaSawit', 'barang', 'dokumen', 'entitas',
            'jaminan', 'kemasan', 'pengangkut', 'pungutan', 'uangMuka', 'vd',
        ], array_keys($p));
        $this->assertArrayNotHasKey('kontainer', $p);
    }

    public function test_root_values(): void
    {
        $p = $this->build($this->header());

        $this->assertSame(1500.0, $p['hargaPenyerahan']);
        $this->assertSame(1500.0, $p['nilaiBarang']);
        $this->assertSame(100.46, $p['netto']);
        $this->assertSame('2026-10-26', $p['tanggalAju']);
        $this->assertSame('261', $p['kodeDokumen']);
        $this->assertSame(0, $p['jumlahKontainer']);
    }

    public function test_empty_document_still_has_fixed_arrays(): void
    {
        $p = $this->build([]);

        $this->assertSame(0, $p['bruto']);
        $this->assertSame(0, $p['netto']);
        $this->assertSame('', $p['tanggalTtd']);
        $this->assertCount(3, $p['entitas']);
        $this->assertSame([3, 7, 8], array_column($p['entitas'], 'seriEntitas'));
        $this->assertSame(['3', '02', '6', '0'], [
            $p['entitas'][0]['kodeEntitas'], $p['entitas'][0]['kodeJenisApi'],
            $p['entitas'][0]['kodeJenisIdentitas'], $p['entitas'][0]['kodeStatus'],
        ]);
        $this->assertCount(1, $p['pengangkut']);
        $this->assertSame([[
            'idPungutan' => '1', 'kodeFasilitasTarif' => '3', 'kodeJenisPungutan' => 'PPN', 'nilaiPungutan' => 0,
        ]], $p['pungutan']);
        $this->assertSame([], $p['barang']);
    }

    public function test_entitas_key_order_per_slot(): void
    {
        $p = $this->build($this->header(), [], [$this->entitas('A'), $this->entitas('B'), $this->entitas('C')]);

        $this->assertSame([
            'alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'namaEntitas', 'kodeStatus',
            'nibEntitas', 'nomorIdentitas', 'nomorIjinEntitas', 'tanggalIjinEntitas', 'seriEntitas',
        ], array_keys($p['entitas'][0]));
        $this->assertSame([
            'alamatEntitas', 'kodeEntitas', 'nomorIdentitas', 'kodeNegara', 'kodeStatus', 'namaEntitas',
            'kodeJenisIdentitas', 'seriEntitas',
        ], array_keys($p['entitas'][1]));
        $this->assertSame([
            'alamatEntitas', 'kodeEntitas', 'kodeJenisApi', 'kodeJenisIdentitas', 'kodeStatus', 'namaEntitas',
            'nomorIdentitas', 'nomorIjinEntitas', 'tanggalIjinEntitas', 'seriEntitas',
        ], array_keys($p['entitas'][2]));
        $this->assertSame(['A', 'B', 'C'], array_column($p['entitas'], 'namaEntitas'));
        $this->assertSame('ID', $p['entitas'][1]['kodeNegara']);
    }

    public function test_entitas_slots_fill_next_non_empty(): void
    {
        // Q-d: entitas 1 kosong -> data entitas 2 masuk slot seri 3.
        $p = $this->build($this->header(), [], [null, $this->entitas('B'), $this->entitas('C')]);

        $this->assertSame('B', $p['entitas'][0]['namaEntitas']);
        $this->assertSame('C', $p['entitas'][1]['namaEntitas']);
        $this->assertSame('', $p['entitas'][2]['namaEntitas']);
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

    public function test_barang_key_order_and_tarif(): void
    {
        $p = $this->build($this->header(), ['barang' => [$this->barang(1)]]);
        $b = $p['barang'][0];

        $this->assertSame([
            'idBarang', 'cif', 'cifRupiah', 'hargaEkspor', 'hargaPenyerahan', 'isiPerKemasan', 'jumlahKemasan',
            'jumlahSatuan', 'kodeAsalBahanBaku', 'kodeAsalBarang', 'kodeBarang', 'kodeDokumen', 'kodeJenisKemasan',
            'kodeNegaraAsal', 'kodeSatuanBarang', 'merk', 'ndpbm', 'netto', 'nilaiBarang', 'nilaiJasa', 'posTarif',
            'seriBarang', 'spesifikasiLain', 'hargaPerolehan', 'tipe', 'uangMuka', 'ukuran', 'uraian', 'diskon',
            'freight', 'hargaSatuan', 'kodeKategoriBarang', 'kodePerhitungan', 'bahanBaku', 'barangTarif',
            'nilaiDiskon', 'volume', 'persenBM', 'persenPPN', 'persenPPh', 'barangDokumen',
        ], array_keys($b));

        $this->assertSame([
            'kodeJenisTarif', 'jumlahSatuan', 'kodeFasilitasTarif', 'kodeJenisPungutan', 'kodeSatuanBarang',
            'nilaiBayar', 'nilaiFasilitas', 'nilaiSudahDilunasi', 'seriBarang', 'tarif', 'tarifFasilitas',
        ], array_keys($b['barangTarif'][0]));
        $ppx = [
            'kodeJenisTarif', 'jumlahSatuan', 'kodeFasilitasTarif', 'kodeSatuanBarang', 'kodeJenisPungutan',
            'nilaiBayar', 'nilaiFasilitas', 'nilaiSudahDilunasi', 'seriBarang', 'tarif', 'tarifFasilitas',
        ];
        $this->assertSame($ppx, array_keys($b['barangTarif'][1]));
        $this->assertSame($ppx, array_keys($b['barangTarif'][2]));
        $this->assertSame(['BM', 'PPH', 'PPN'], array_column($b['barangTarif'], 'kodeJenisPungutan'));
        $this->assertSame([40, 2.5, 11], array_column($b['barangTarif'], 'tarif'));

        $this->assertSame(0.0, $b['cif']);
        $this->assertSame(0, $b['ndpbm']);
        $this->assertSame(0, $b['hargaEkspor']);
        $this->assertNull($b['volume']);
        $this->assertSame(24000000.0, $p['pungutan'][0]['nilaiPungutan']);
    }

    public function test_bahan_baku_grouped_per_barang_as_list(): void
    {
        $p = $this->build($this->header(), [
            'barang' => [$this->barang(2), $this->barang(1)],
            'bahanbaku' => [
                $this->bahanBaku(1, 1),
                $this->bahanBaku(2, 1, ['kodeAsalBahanBaku' => '1']),
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
        $this->assertSame([
            'jumlahKemasan', 'jumlahSatuan', 'kodeAsalBahanBaku', 'kodeFasilitasTarif', 'kodeJenisPungutan',
            'kodeJenisTarif', 'kodeSatuanBarang', 'nilaiBayar', 'nilaiFasilitas', 'nilaiSudahDilunasi',
            'seriBahanBaku', 'tarif', 'tarifFasilitas',
        ], array_keys($bb['bahanBakuTarif'][0]));
        $this->assertSame('1', $bb['kodeAsalBahanBaku']);
        $this->assertSame('23', $bb['kodeDokAsal']);
        $this->assertSame('071100', $bb['kodeKantor']);
        $this->assertSame('123456', $bb['nomorDokumen']);
        $this->assertSame(1, $bb['seriBarangDokAsal']);
        $this->assertSame(1, $bb['seriIjin']);
        $this->assertSame(1, $p['barang'][1]['bahanBaku'][1]['bahanBakuTarif'][0]['seriBahanBaku']);
    }

    public function test_other_collections(): void
    {
        $p = $this->build($this->header(), [
            'kemasan' => [['seriKemasan' => '1', 'jumlahKemasan' => 3, 'kodeJenisKemasan' => 'BX', 'merkKemasan' => '']],
            'dokumen' => [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1', 'tanggalDokumen' => null]],
            'jaminan' => [['kodeJenisJaminan' => '2', 'nilaiJaminan' => 5000000, 'tanggalJaminan' => '2026-10-01']],
        ]);

        $this->assertSame([['jumlahKemasan' => 3, 'kodeJenisKemasan' => 'BX', 'seriKemasan' => '1', 'merkKemasan' => '']], $p['kemasan']);
        $this->assertSame([[
            'idDokumen' => '0', 'kodeDokumen' => '380', 'nomorDokumen' => 'INV/1', 'seriDokumen' => 1, 'tanggalDokumen' => null,
        ]], $p['dokumen']);
        $this->assertSame([
            'idJaminan', 'kodeJenisJaminan', 'nilaiJaminan', 'nomorBpj', 'nomorJaminan', 'penjamin',
            'tanggalBpj', 'tanggalJaminan', 'tanggalJatuhTempo',
        ], array_keys($p['jaminan'][0]));
        $this->assertSame('2', $p['jaminan'][0]['idJaminan']);
        $this->assertSame(5000000, $p['jaminan'][0]['nilaiJaminan']);
        $this->assertSame([
            'idPengangkut' => '1', 'kodeBendera' => '', 'namaPengangkut' => '', 'nomorPengangkut' => '',
            'kodeCaraAngkut' => '3', 'seriPengangkut' => 1,
        ], $p['pengangkut'][0]);
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
