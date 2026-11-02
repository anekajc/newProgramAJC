<?php

namespace Tests\Unit;

use App\Services\BeaCukai\Bc23PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use PHPUnit\Framework\TestCase;

// Pemeriksa payload terhadap skema resmi CEISA (docs/bc_schema/bc23.json). Memakai payload hasil
// Bc23PayloadBuilder yang lengkap sebagai dasar, lalu merusaknya satu per satu.
class CeisaSchemaValidatorTest extends TestCase
{
    private const WAJIB_ISI = [
        'kodeKantor', 'kodeKantorBongkar', 'kodePelMuat', 'kodePelBongkar', 'kodeTps', 'kodeTujuanTpb', 'kodeValuta',
        'kodeIncoterm', 'nomorBc11', 'posBc11', 'subposBc11', 'kotaTtd', 'namaTtd', 'jabatanTtd',
        'barang.kodeBarang', 'barang.uraian', 'barang.posTarif', 'barang.kodeSatuanBarang', 'barang.kodeJenisKemasan',
        'barang.kodeKategoriBarang', 'barang.kodeNegaraAsal',
        'entitas.namaEntitas', 'entitas.alamatEntitas', 'entitas.nomorIdentitas',
        'dokumen.kodeDokumen', 'dokumen.nomorDokumen',
    ];

    private function opsi(): array
    {
        return [
            'bagian' => [
                'barang' => 'Barang', 'barangTarif' => ['Tarif BM', 'Tarif PPH', 'Tarif PPN'], 'dokumen' => 'Dokumen',
                'entitas' => ['Importir / Pengusaha TPB', 'Pemasok', 'Pemilik Barang'], 'kemasan' => 'Kemasan',
                'kontainer' => 'Peti Kemas', 'pengangkut' => 'Pengangkut',
            ],
            'wajibIsi' => self::WAJIB_ISI,
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer'],
        ];
    }

    private function lengkap(): array
    {
        $header = [
            'nomorAju' => '00002320261029000001ABCDEF', 'kodeKantor' => '040300', 'kodeKantorBongkar' => '040300',
            'kodeTujuanTpb' => '1', 'nomorBc11' => '123456', 'tanggalBc11' => '2026-10-20', 'posBc11' => '0001',
            'subPosBc11' => '00000000', 'kodePelMuat' => 'MYPKG', 'kodePelTransit' => '', 'kodePelBongkar' => 'IDTPP',
            'kodeTps' => 'TPS1', 'tanggalTiba' => '2026-10-25', 'kodeValuta' => 'MYR', 'ndpbm' => 3800.1234,
            'kodeIncoterm' => 'CIF', 'fob' => 1000.0, 'freight' => 50.0, 'asuransi' => 10.0, 'kodeAsuransi' => 'LN',
            'cif' => 1060.0, 'bruto' => 120.5, 'netto' => 100.25, 'kodeCaraAngkut' => '1', 'namaPengangkut' => 'MV X',
            'nomorPengangkut' => 'V1', 'kodeBendera' => 'MY', 'jumlahKemasan' => 10, 'kodeJenisKemasan' => 'BX',
            'merkKemasan' => '', 'nomorKontainer' => 'ABCD1234567', 'kodeUkuranKontainer' => '20', 'kodeTipeKontainer' => '1',
            'kodeJenisKontainer' => '8', 'kotaTtd' => 'Jember', 'tanggalTtd' => '2026-10-26', 'namaTtd' => 'Budi', 'jabatanTtd' => 'Manager',
        ];
        $barang = [[
            'seriBarang' => 1, 'posTarif' => '84879000', 'kodeBarang' => 'SP-01', 'uraian' => 'BEARING', 'merk' => 'SKF',
            'kodeSatuanBarang' => 'PCE', 'jumlahSatuan' => 10.0, 'jumlahKemasan' => 10.0, 'kodeJenisKemasan' => 'BX',
            'netto' => 100.25, 'cif' => 1060.0, 'cifRupiah' => 4028000.0, 'ndpbm' => 3800.1234, 'fob' => 1000.0,
            'asuransi' => 10.0, 'freight' => 50.0, 'kodeKategoriBarang' => '11', 'kodeNegaraAsal' => 'MY',
        ]];
        $dokumen = [
            ['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-01'],
            ['seriDokumen' => 2, 'kodeDokumen' => '705', 'nomorDokumen' => 'BL-1', 'tanggalDokumen' => '2026-10-10'],
        ];
        $pengusaha = ['namaEntitas' => 'PT AJC', 'alamatEntitas' => 'Jember', 'nomorIdentitas' => '1234567890123456', 'nibEntitas' => '123',
            'nomorIjinEntitas' => 'KEP-1', 'tanggalIjinEntitas' => '2020-01-01', 'kodeJenisApi' => '02', 'kodeJenisIdentitas' => '6', 'kodeStatus' => '5'];
        $pemasok = ['namaEntitas' => 'SUPPLIER SDN BHD', 'alamatEntitas' => 'Penang', 'kodeNegara' => 'MY'];

        return (new Bc23PayloadBuilder)->build($header, ['barang' => $barang, 'dokumen' => $dokumen], [$pengusaha, $pemasok, $pengusaha]);
    }

    private function masalah(array $payload): array
    {
        return (new CeisaSchemaValidator)->masalah('bc23', $payload, $this->opsi());
    }

    private function adaMasalah(array $masalah, string $potongan): bool
    {
        foreach ($masalah as $m) {
            if (str_contains($m, $potongan)) {
                return true;
            }
        }

        return false;
    }

    public function test_complete_payload_passes(): void
    {
        $this->assertSame([], $this->masalah($this->lengkap()));
    }

    public function test_nomor_aju_must_be_exactly_26_alphanumeric(): void
    {
        foreach (['TEST41', '00002320261029000001ABCDE', '00002320261029000001ABCDEFG', '00002320261029000001-BCDEF'] as $aju) {
            $p = $this->lengkap();
            $p['nomorAju'] = $aju;
            $this->assertTrue($this->adaMasalah($this->masalah($p), 'Nomor AJU (nomorAju): harus tepat 26 karakter huruf/angka'), $aju);
        }
        $p = $this->lengkap();
        $p['nomorAju'] = null;
        $this->assertTrue($this->adaMasalah($this->masalah($p), 'Nomor AJU (nomorAju): wajib diisi'));
    }

    public function test_blank_required_text_dates_and_codes_are_listed(): void
    {
        $p = $this->lengkap();
        $p['kodeKantor'] = null;
        $p['kotaTtd'] = '';
        $p['tanggalBc11'] = '';
        $p['kodeTps'] = '  ';
        $p['kodePelTransit'] = ''; // boleh kosong
        $masalah = $this->masalah($p);

        $this->assertTrue($this->adaMasalah($masalah, 'Kantor Pabean (kodeKantor): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Kota (Pernyataan) (kotaTtd): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Tanggal BC 1.1 (tanggalBc11): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Tempat Penimbunan (TPS) (kodeTps): wajib diisi'));
        $this->assertFalse($this->adaMasalah($masalah, 'kodePelTransit'));
        $this->assertCount(4, $masalah);
    }

    public function test_dokumen_are_checked_per_position(): void
    {
        $p = $this->lengkap();
        $p['dokumen'] = array_reverse($p['dokumen']);
        $masalah = $this->masalah($p);
        $this->assertTrue($this->adaMasalah($masalah, "Dokumen #1 > Jenis Dokumen (kodeDokumen): harus berisi '380'"));
        $this->assertTrue($this->adaMasalah($masalah, 'Dokumen #2 > Jenis Dokumen (kodeDokumen): harus salah satu dari: 705, 740'));

        // Dokumen ke-3 dst. bebas jenisnya tetapi nomor / tanggal wajib.
        $p = $this->lengkap();
        $p['dokumen'][] = ['idDokumen' => '0', 'kodeDokumen' => '217', 'nomorDokumen' => null, 'seriDokumen' => 3, 'tanggalDokumen' => null];
        $masalah = $this->masalah($p);
        $this->assertTrue($this->adaMasalah($masalah, 'Dokumen #3 > Nomor (nomorDokumen): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Dokumen #3 > Tanggal (tanggalDokumen): wajib diisi'));
    }

    public function test_barang_rows_all_checked(): void
    {
        $p = $this->lengkap();
        $p['barang'][1] = $p['barang'][0];
        $p['barang'][1]['seriBarang'] = 2;
        $p['barang'][1]['kodeNegaraAsal'] = '';
        $p['barang'][1]['kodeKategoriBarang'] = '';
        $masalah = $this->masalah($p);

        $this->assertTrue($this->adaMasalah($masalah, 'Barang #2 > Negara Asal (kodeNegaraAsal): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Barang #2 > Kategori Barang (kodeKategoriBarang): wajib diisi'));
        $this->assertFalse($this->adaMasalah($masalah, 'Barang #1'));

        $p = $this->lengkap();
        $p['barang'][0]['kodeNegaraAsal'] = 'my';
        $this->assertTrue($this->adaMasalah($this->masalah($p), 'harus 2 huruf kapital'));
    }

    public function test_entitas_and_kontainer_problems(): void
    {
        $p = $this->lengkap();
        $p['entitas'][1]['kodeNegara'] = '';
        $p['entitas'][0]['tanggalIjinEntitas'] = '';
        $p['kontainer'][0]['kodeJenisKontainer'] = '';
        $masalah = $this->masalah($p);

        $this->assertTrue($this->adaMasalah($masalah, 'Pemasok > Negara (kodeNegara): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Importir / Pengusaha TPB > Tanggal Izin (tanggalIjinEntitas): wajib diisi'));
        $this->assertTrue($this->adaMasalah($masalah, 'Peti Kemas #1 > Jenis Muatan (kodeJenisKontainer): wajib dipilih'));
    }

    public function test_decimals_are_checked(): void
    {
        $p = $this->lengkap();
        $p['cif'] = 1234.5678;
        $p['ndpbm'] = 15000.12345;
        $masalah = $this->masalah($p);

        $this->assertTrue($this->adaMasalah($masalah, 'Nilai CIF (cif): maksimal 2 angka di belakang koma'));
        $this->assertTrue($this->adaMasalah($masalah, 'NDPBM (ndpbm): maksimal 4 angka di belakang koma'));
    }

    public function test_missing_schema_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        (new CeisaSchemaValidator)->masalah('bc99', []);
    }
}
