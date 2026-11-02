<?php

namespace Tests\Unit;

use App\Http\Controllers\DocumentBc\DocumentBc40Controller;
use App\Services\BeaCukai\Bc40PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

// Payload BC 4.0 dari Bc40PayloadBuilder diperiksa terhadap skema resmi docs/bc_schema/bc40.json, memakai opsi
// yang sama dengan DocumentBc40Controller::masalahKirim (daftar WAJIB_ISI dibaca dari controller).
class Bc40SchemaTest extends TestCase
{
    private function opsi(): array
    {
        return [
            'bagian' => [
                'barang' => 'Barang', 'barangTarif' => ['Tarif PPN'], 'dokumen' => 'Dokumen', 'kemasan' => 'Kemasan',
                'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Pengirim Barang'], 'pengangkut' => 'Pengangkut', 'pungutan' => 'Pungutan',
            ],
            'wajibIsi' => (new ReflectionClass(DocumentBc40Controller::class))->getReflectionConstant('WAJIB_ISI')->getValue(),
            'sisa' => ['barang', 'dokumen'],
        ];
    }

    private function header(array $override = []): array
    {
        return array_merge([
            'nomorAju' => '00004020261029000001ABCDEF', 'kodeKantor' => '071100', 'kodeJenisTpb' => '1', 'kodeTujuanPengirim' => '1',
            'tanggalAju' => '2026-10-29', 'hargaPenyerahan' => 15000000.0, 'nilaiJasa' => 0.0, 'uangMuka' => 0.0, 'bruto' => 520.0,
            'netto' => 500.0, 'volume' => 0.0, 'namaPengangkut' => 'TRUCK', 'nomorPengangkut' => 'P 1234 AB',
            'kodeJenisKemasan' => 'BX', 'jumlahKemasan' => 10, 'merkKemasan' => '-', 'kotaTtd' => 'Jember', 'tanggalTtd' => '2026-10-29',
            'namaTtd' => 'Budi', 'jabatanTtd' => 'Manager', 'idPengguna' => 'erp_user',
        ], $override);
    }

    private function barang(array $override = []): array
    {
        return array_merge([
            'seriBarang' => 1, 'posTarif' => '84879000', 'kodeBarang' => 'SP-01', 'uraian' => 'BEARING', 'merk' => '', 'tipe' => '',
            'ukuran' => '', 'spesifikasiLain' => '', 'kodeSatuanBarang' => 'PCE', 'jumlahSatuan' => 100.0, 'kodeJenisKemasan' => 'BX',
            'jumlahKemasan' => 10, 'netto' => 500.0, 'volume' => 0.0, 'hargaPenyerahan' => 15000000.0,
        ], $override);
    }

    private function entitas(): array
    {
        $pihak = ['namaEntitas' => 'PT X', 'alamatEntitas' => 'Jember', 'nomorIdentitas' => '1234567890123456', 'nibEntitas' => '123',
            'kodeJenisIdentitas' => '6', 'kodeJenisApi' => '02', 'kodeStatus' => '0'];

        return [$pihak + ['nomorIjinEntitas' => 'KEP-1', 'tanggalIjinEntitas' => '2020-01-01'], $pihak, $pihak];
    }

    private function dokumen(): array
    {
        return [['seriDokumen' => 1, 'kodeDokumen' => '380', 'nomorDokumen' => 'INV-1', 'tanggalDokumen' => '2026-10-20']];
    }

    private function masalah(array $header = [], ?array $barang = null, ?array $entitas = null, ?array $dokumen = null): array
    {
        $payload = (new Bc40PayloadBuilder)->build(
            $this->header($header),
            ['barang' => $barang ?? [$this->barang()], 'dokumen' => $dokumen ?? $this->dokumen()],
            $entitas ?? $this->entitas()
        );

        return (new CeisaSchemaValidator)->masalah('bc40', $payload, $this->opsi());
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

    public function test_complete_document_passes_the_official_schema(): void
    {
        $this->assertSame([], $this->masalah());
    }

    public function test_nomor_aju_must_be_exactly_26_alphanumerics(): void
    {
        $this->assertTrue($this->mengandung($this->masalah(['nomorAju' => '000040-AJU-1']), 'Nomor AJU (nomorAju): harus tepat 26'));
        $this->assertTrue($this->mengandung($this->masalah(['nomorAju' => '']), 'Nomor AJU (nomorAju): wajib diisi'));
        $this->assertTrue($this->mengandung($this->masalah(['nomorAju' => null]), 'Nomor AJU (nomorAju): wajib diisi'));
    }

    public function test_empty_header_fields_are_reported(): void
    {
        $m = $this->masalah(['tanggalAju' => null, 'tanggalTtd' => null, 'kodeKantor' => null, 'kotaTtd' => '', 'namaTtd' => null, 'jabatanTtd' => null]);

        foreach (['Tanggal Aju', 'Tanggal (Pernyataan)', 'Kantor Pabean', 'Kota (Pernyataan)', 'Nama (Pernyataan)', 'Jabatan (Pernyataan)'] as $label) {
            $this->assertTrue($this->mengandung($m, "{$label} "), $label);
        }
    }

    public function test_unselected_entitas_slot_is_reported_per_party(): void
    {
        $e = $this->entitas();
        $m = $this->masalah([], null, [null, $e[1], $e[2]]);

        $this->assertTrue($this->mengandung($m, 'Pengusaha TPB > Nama (namaEntitas): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Pengusaha TPB > Tanggal Izin (tanggalIjinEntitas): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Pengusaha TPB > Nomor Izin (nomorIjinEntitas): wajib diisi'));
        $this->assertFalse($this->mengandung($m, 'Pemilik Barang'));

        $m = $this->masalah([], null, [$e[0], $e[1], null]);
        $this->assertTrue($this->mengandung($m, 'Pengirim Barang > NIB (nibEntitas): wajib diisi'));
    }

    public function test_dokumen_is_optional_but_existing_rows_must_be_complete(): void
    {
        $this->assertSame([], $this->masalah([], null, null, []), 'tanpa dokumen boleh');

        $m = $this->masalah([], null, null, [['seriDokumen' => 1, 'kodeDokumen' => '', 'nomorDokumen' => null, 'tanggalDokumen' => null]]);
        $this->assertTrue($this->mengandung($m, 'Dokumen #1 > Jenis Dokumen (kodeDokumen): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Dokumen #1 > Nomor (nomorDokumen): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Dokumen #1 > Tanggal (tanggalDokumen): wajib diisi'));
    }

    public function test_empty_barang_and_pengangkut_text_is_reported(): void
    {
        $m = $this->masalah(['namaPengangkut' => '', 'nomorPengangkut' => ''], [$this->barang(['kodeBarang' => '', 'posTarif' => '', 'kodeJenisKemasan' => ''])]);

        $this->assertTrue($this->mengandung($m, 'Pengangkut #1 > Nama Sarana Pengangkut (namaPengangkut): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Barang #1 > Kode Barang (kodeBarang): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Barang #1 > Pos Tarif / HS (posTarif): wajib diisi'));
        $this->assertTrue($this->mengandung($m, 'Barang #1 > Jenis Kemasan (kodeJenisKemasan): wajib diisi'));
    }

    // Merk / tipe / ukuran / spesifikasi lain barang dan merk kemasan boleh kosong.
    public function test_optional_texts_may_stay_empty(): void
    {
        $this->assertSame([], $this->masalah(['merkKemasan' => '']));
    }

    public function test_split_amounts_stay_within_four_decimals(): void
    {
        $barang = [
            $this->barang(['seriBarang' => 1, 'netto' => 10.0, 'hargaPenyerahan' => 100.0]),
            $this->barang(['seriBarang' => 2, 'netto' => 10.0, 'hargaPenyerahan' => 100.0]),
            $this->barang(['seriBarang' => 3, 'netto' => 10.0, 'hargaPenyerahan' => 100.0]),
        ];

        $this->assertSame([], $this->masalah(['bruto' => 100.0, 'uangMuka' => 1000.0, 'nilaiJasa' => 10.0], $barang));
    }
}
