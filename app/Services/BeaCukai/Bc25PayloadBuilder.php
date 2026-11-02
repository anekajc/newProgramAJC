<?php

namespace App\Services\BeaCukai;

use App\Services\BeaCukai\Concerns\ValutaIso;
use Illuminate\Container\Container;

// Penyusun JSON CEISA untuk BC 2.5 - mengikuti skema RESMI docs/bc_schema/bc25.json (menggantikan daftar key
// Villiger di docs/documentbc25_plan.md §9; bentuknya jauh berbeda). Setiap objek disusun manual dengan urutan
// key tetap. Jangan pakai array_merge/toArray untuk menyusunnya. Sengaja dipisah dari builder BC lain supaya
// perubahan di satu dokumen tidak mengubah dokumen lain.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase yang nilainya sudah bertipe PHP -
// desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null. Konversi dari
// baris SQL Server dikerjakan controller.
//
// Beda dari Villiger / plan (disepakati dengan user setelah pengecekan skema resmi):
//   - key yang tidak ada di skema tidak dikirim: root (asuransi, freight, biayaTambahan, biayaPengurang, uangMuka,
//     nilaiBarang, nik, vd, jaminan, pungutan), barang (idBarang, hargaSatuan, kodeAsalBahanBaku, kodeAsalBarang,
//     kodeDokumen, kodeNegaraAsal, nilaiDiskon, nilaiJasa, uangMuka), bahan baku (kodeDokumen, netto, nilaiJasa),
//     entitas (kodeNegara Pemilik, izin Penerima) dan pengangkut (idPengangkut, kodeBendera). Tab Pungutan (12 nilai
//     manual) dihapus karena skema tidak punya array pungutan;
//   - key wajib baru: volume (root), fob 0 / kodeGunaBarang / kodeKondisiBarang / kodeDokAsal / kodeKategoriBarang
//     (barang), bahanBakuTarif (kosong dulu), niperEntitas (Penerima Barang) dan idPengguna = username CEISA dari
//     Login BC terakhir ($header['idPengguna'], diisi controller; kosong bila belum login);
//   - entitas slot tetap E1->seri 3, E2->seri 7, E3->seri 8; data entitas dari salinan di dokumen (Q-f);
//   - kodeValuta diterjemahkan ke kode ISO (config beacukai.valuta_iso); nilai root bersifat 2 desimal dibulatkan;
//   - jumlahKontainer = banyaknya peti kemas; tarif barang dihitung dari persen HS barang (Q-b), CIF barang =
//     CIF Rupiah / NDPBM (Q-c, dihitung di form).
// Konstanta fasilitas/tarif dan kodeLokasiBayar dibaca dari config/beacukai.php (bc25.*).
class Bc25PayloadBuilder
{
    use ValutaIso;

    /**
     * @param  array  $header  header dokumen (§2.1)
     * @param  array  $children  ['barang' => [...], 'bahanbaku' => [...], 'kemasan' => [...], 'kontainer' => [...], 'dokumen' => [...]]
     * @param  array  $entitas  [entitas 1 (Pengusaha TPB), 2 (Pemilik Barang), 3 (Penerima Barang)] dari salinan di dokumen; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));
        $bahanBaku = array_values($children['bahanbaku'] ?? []);
        $kontainer = array_values($children['kontainer'] ?? []);

        return [
            'asalData' => 'S',
            'bruto' => (float) self::orDefault($header['bruto'] ?? null, 0),
            'cif' => $this->flt2($header['cif'] ?? null),
            'dasarPengenaanPajak' => $this->flt2($header['dasarPengenaanPajak'] ?? null),
            'disclaimer' => '1',
            'hargaPenyerahan' => (float) self::orDefault($header['hargaPenyerahan'] ?? null, 0),
            'idPengguna' => (string) ($header['idPengguna'] ?? ''),
            'jabatanTtd' => (string) ($header['jabatanTtd'] ?? ''),
            'jumlahKontainer' => count($kontainer),
            'kodeDokumen' => '25',
            'kodeKantor' => (string) ($header['kodeKantor'] ?? ''),
            'kodeLokasiBayar' => (string) $this->cfg('kode_lokasi_bayar', '1'),
            'kodeTujuanPengiriman' => (string) ($header['kodeTujuanPengiriman'] ?? ''),
            'kodeJenisTpb' => (string) self::orDefault($header['kodeJenisTpb'] ?? null, '1'),
            'kodeCaraBayar' => (string) self::orDefault($header['kodeCaraBayar'] ?? null, '1'),
            'kodeValuta' => $this->valutaIso((string) self::orDefault($header['kodeValuta'] ?? null, '')),
            'kotaTtd' => (string) ($header['kotaTtd'] ?? ''),
            'namaTtd' => (string) ($header['namaTtd'] ?? ''),
            'ndpbm' => (float) self::orDefault($header['ndpbm'] ?? null, 0),
            'netto' => $this->flt4($header['netto'] ?? null),
            'nomorAju' => (string) ($header['nomorAju'] ?? ''),
            'ppnPajak' => $this->flt2($header['ppnPajak'] ?? null),
            'ppnbmPajak' => $this->flt2($header['ppnbmPajak'] ?? null),
            'seri' => 1,
            'tanggalAju' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'tanggalTtd' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'tarifPpnPajak' => $this->flt2($header['tarifPpnPajak'] ?? null),
            'tarifPpnbmPajak' => $this->flt2($header['tarifPpnbmPajak'] ?? null),
            'volume' => (float) self::orDefault($header['volume'] ?? null, 0),
            'barang' => array_map(fn ($row) => $this->barang($row, $bahanBaku), $barang),
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'entitas' => $this->entitas($entitas),
            'kemasan' => array_map(fn ($row) => $this->kemasan($row), array_values($children['kemasan'] ?? [])),
            'kontainer' => array_map(fn ($row) => $this->kontainer($row), $kontainer),
            'pengangkut' => [[
                'namaPengangkut' => (string) ($header['namaPengangkut'] ?? ''),
                'nomorPengangkut' => (string) ($header['nomorPengangkut'] ?? ''),
                'kodeCaraAngkut' => (string) self::orDefault($header['kodeCaraAngkut'] ?? null, ''),
                'seriPengangkut' => 1,
            ]],
        ];
    }

    public function encode(array $payload, bool $pretty = false): string
    {
        $flags = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        }

        return json_encode($payload, $flags);
    }

    // Fallback hanya untuk null, "", 0 dan 0.0. String "0" BUKAN kosong (beda dengan ?: milik PHP).
    public static function orDefault($value, $default)
    {
        if ($value === null || $value === '' || $value === 0 || $value === 0.0) {
            return $default;
        }

        return $value;
    }

    // Konstanta dari config/beacukai.php (bc25.*); bawaan Villiger bila container belum punya config
    // (mis. dipakai di luar aplikasi).
    private function cfg(string $key, $default)
    {
        $container = Container::getInstance();

        return $container->bound('config') ? $container->make('config')->get("beacukai.bc25.{$key}", $default) : $default;
    }

    // Slot tetap seri 3 (Pengusaha TPB) -> 7 (Pemilik Barang) -> 8 (Penerima Barang), mengikuti skema resmi:
    // Pemilik tidak punya key negara; Penerima punya niperEntitas dan tidak mengirim izin.
    private function entitas(array $entitas): array
    {
        $e = fn (int $i, string $key, $default = '') => ! empty($entitas[$i]) ? self::orDefault($entitas[$i][$key] ?? null, $default) : $default;
        $tgl = fn (int $i) => ! empty($entitas[$i]) ? ($this->date($entitas[$i]['tanggalIjinEntitas'] ?? null) ?? '') : '';
        $dasar = fn (int $i, string $kode, int $seri) => [
            'alamatEntitas' => (string) $e($i, 'alamatEntitas'),
            'kodeEntitas' => $kode,
            'kodeJenisIdentitas' => (string) $e($i, 'kodeJenisIdentitas', '6'),
            'kodeStatus' => (string) $e($i, 'kodeStatus', '0'),
            'namaEntitas' => (string) $e($i, 'namaEntitas'),
            'nomorIdentitas' => (string) $e($i, 'nomorIdentitas'),
            'seriEntitas' => $seri,
        ];

        return [
            $dasar(0, '3', 3) + [
                'kodeJenisApi' => (string) $e(0, 'kodeJenisApi', '02'),
                'nomorIjinEntitas' => (string) $e(0, 'nomorIjinEntitas'),
                'tanggalIjinEntitas' => $tgl(0),
                'nibEntitas' => (string) $e(0, 'nibEntitas'),
            ],
            $dasar(1, '7', 7),
            $dasar(2, '8', 8) + [
                'kodeJenisApi' => (string) $e(2, 'kodeJenisApi', '02'),
                'niperEntitas' => (string) $e(2, 'niperEntitas'),
            ],
        ];
    }

    private function dokumen(array $row): array
    {
        return [
            'idDokumen' => '0',
            'kodeDokumen' => (string) ($row['kodeDokumen'] ?? ''),
            'nomorDokumen' => $this->strOrNull($row['nomorDokumen'] ?? null),
            'seriDokumen' => (int) ($row['seriDokumen'] ?? 0),
            'tanggalDokumen' => $this->date($row['tanggalDokumen'] ?? null),
        ];
    }

    // seriKemasan dikirim sebagai angka; fallback 1. Skema melarang key lain (additionalProperties false).
    private function kemasan(array $row): array
    {
        return [
            'jumlahKemasan' => (int) self::orDefault($row['jumlahKemasan'] ?? null, 0),
            'kodeJenisKemasan' => (string) self::orDefault($row['kodeJenisKemasan'] ?? null, ''),
            'seriKemasan' => (int) self::orDefault($row['seriKemasan'] ?? null, 1),
            'merkKemasan' => (string) self::orDefault($row['merkKemasan'] ?? null, ''),
        ];
    }

    private function kontainer(array $row): array
    {
        return [
            'kodeJenisKontainer' => $this->strOrNull($row['kodeJenisKontainer'] ?? null),
            'kodeTipeKontainer' => $this->strOrNull($row['kodeTipeKontainer'] ?? null),
            'kodeUkuranKontainer' => $this->strOrNull($row['kodeUkuranKontainer'] ?? null),
            'nomorKontainer' => $this->strOrNull($row['nomorKontainer'] ?? null),
            'seriKontainer' => (int) ($row['seriKontainer'] ?? 0),
        ];
    }

    // Key barang mengikuti skema resmi: tanpa kodeNegaraAsal / idBarang / hargaSatuan / nilaiJasa dst; fob selalu 0.
    private function barang(array $row, array $semuaBahanBaku): array
    {
        $seri = (int) ($row['seriBarang'] ?? 0);
        $bahanBaku = array_values(array_filter($semuaBahanBaku, fn ($bb) => (int) ($bb['seriBarang'] ?? 0) === $seri));
        $cifRupiah = $row['cifRupiah'] ?? null;

        return [
            'cif' => (float) ($row['cif'] ?? 0),
            'cifRupiah' => round((float) ($cifRupiah ?? 0), 2),
            'diskon' => $this->num($row['diskon'] ?? null, 0),
            'fob' => 0,
            'freight' => $this->num($row['freight'] ?? null, 0),
            'hargaEkspor' => $this->num($row['hargaEkspor'] ?? null, 0),
            'hargaPenyerahan' => $this->num($row['hargaPenyerahan'] ?? null, 0),
            'hargaPerolehan' => $this->num($row['hargaPerolehan'] ?? null, 0),
            'isiPerKemasan' => $this->num($row['isiPerKemasan'] ?? null, 0),
            'jumlahKemasan' => $this->round2($row['jumlahKemasan'] ?? null, 0.0),
            'jumlahSatuan' => (float) ($row['jumlahSatuan'] ?? 0),
            'kodeBarang' => (string) ($row['kodeBarang'] ?? ''),
            'kodeDokAsal' => (string) ($row['kodeDokAsal'] ?? ''),
            'kodeGunaBarang' => (string) ($row['kodeGunaBarang'] ?? ''),
            'kodeJenisKemasan' => (string) ($row['kodeJenisKemasan'] ?? ''),
            'kodeKategoriBarang' => (string) ($row['kodeKategoriBarang'] ?? ''),
            'kodeKondisiBarang' => (string) ($row['kodeKondisiBarang'] ?? ''),
            'kodePerhitungan' => (string) self::orDefault($row['kodePerhitungan'] ?? null, '0'),
            'kodeSatuanBarang' => (string) ($row['kodeSatuanBarang'] ?? ''),
            'merk' => (string) self::orDefault($row['merk'] ?? null, '-'),
            'ndpbm' => $this->num($row['ndpbm'] ?? null, 0),
            'netto' => $this->flt4($row['netto'] ?? null),
            'nilaiBarang' => $this->round2($row['nilaiBarang'] ?? null, 0),
            'posTarif' => (string) ($row['posTarif'] ?? ''),
            'seriBarang' => $seri,
            'spesifikasiLain' => (string) self::orDefault($row['spesifikasiLain'] ?? null, '0'),
            'tipe' => (string) self::orDefault($row['tipe'] ?? null, '0'),
            'ukuran' => (string) self::orDefault($row['ukuran'] ?? null, '0'),
            'uraian' => (string) ($row['uraian'] ?? ''),
            'bahanBaku' => array_map(fn ($bb) => $this->bahanBaku($bb), $bahanBaku),
            'barangTarif' => $this->barangTarif($row),
            'barangDokumen' => [],
        ];
    }

    // DIHITUNG per barang, selalu 3 objek urutan BM, PPN, PPH dengan urutan key yang sama.
    //   BM = CIF Rupiah x persenBM / 100;  dasar = CIF Rupiah + BM;  PPN = dasar x persenPPN / 100;
    //   PPH = dasar x persenPPh / 100. seriBarang = nomor baris tarif (1/2/3), bukan seri barang.
    private function barangTarif(array $row): array
    {
        $cifRp = (float) ($row['cifRupiah'] ?? 0);
        $persenBm = (float) ($row['persenBM'] ?? 0);
        $persenPpn = (float) ($row['persenPPN'] ?? 0);
        $persenPph = (float) ($row['persenPPh'] ?? 0);
        $bm = $cifRp * $persenBm / 100;
        $dasar = $cifRp + $bm;
        $satuan = (string) ($row['kodeSatuanBarang'] ?? '');
        $jumlah = $this->flt4($row['netto'] ?? null);

        $baris = fn (string $pungutan, float $tarif, float $nilai, int $urut) => [
            'kodeJenisTarif' => (string) $this->cfg('kode_jenis_tarif', '1'),
            'kodeFasilitasTarif' => (string) $this->cfg('kode_fasilitas_tarif', '1'),
            'kodeJenisPungutan' => $pungutan,
            'kodeSatuanBarang' => $satuan,
            'jumlahSatuan' => $jumlah,
            'tarif' => round($tarif, 2),
            'tarifFasilitas' => $this->cfg('tarif_fasilitas', 0),
            'nilaiBayar' => round($nilai, 2),
            'nilaiFasilitas' => 0,
            'nilaiSudahDilunasi' => 0,
            'seriBarang' => $urut,
        ];

        return [
            $baris('BM', $persenBm, $bm, 1),
            $baris('PPN', $persenPpn, $dasar * $persenPpn / 100, 2),
            $baris('PPH', $persenPph, $dasar * $persenPph / 100, 3),
        ];
    }

    // Bahan baku mengikuti skema resmi: tanpa kodeDokumen / netto / nilaiJasa; bahanBakuTarif wajib ada dan
    // sementara dikirim kosong (keputusan user). kodeAsalBahanBaku selalu "0" (tidak ada isian asal).
    private function bahanBaku(array $bb): array
    {
        return [
            'cif' => $this->num($bb['cif'] ?? null, 0),
            'cifRupiah' => $this->round2($bb['cifRupiah'] ?? null, 0),
            'hargaPenyerahan' => $this->num($bb['hargaPenyerahan'] ?? null, 0),
            'hargaPerolehan' => $this->num($bb['hargaPerolehan'] ?? null, 0),
            'jumlahSatuan' => $this->num($bb['jumlahSatuan'] ?? null, 0),
            'kodeSatuanBarang' => (string) ($bb['kodeSatuanBarang'] ?? ''),
            'kodeAsalBahanBaku' => '0',
            'kodeBarang' => (string) ($bb['kodeBarang'] ?? ''),
            'kodeDokAsal' => (string) ($bb['kodeDokumen'] ?? ''),
            'kodeKantor' => (string) ($bb['kodeKantorAsal'] ?? ''),
            'merkBarang' => (string) self::orDefault($bb['merkBarang'] ?? null, ''),
            'ndpbm' => 0,
            'nomorAjuDokAsal' => (string) ($bb['nomorAjuDokAsal'] ?? ''),
            'nomorDaftarDokAsal' => (string) ($bb['nomorDaftarDokAsal'] ?? ''),
            'posTarif' => (string) ($bb['posTarif'] ?? ''),
            'seriBahanBaku' => (int) ($bb['seriBahanBaku'] ?? 0),
            'seriBarang' => (int) ($bb['seriBarang'] ?? 0),
            'seriBarangDokAsal' => (int) self::orDefault($bb['seriBarangDokAsal'] ?? null, 1),
            'seriIjin' => 1,
            'spesifikasiLainBarang' => (string) self::orDefault($bb['spesifikasiLainBarang'] ?? null, ''),
            'tanggalDaftarDokAsal' => $this->date($bb['tanggalDaftarDokAsal'] ?? null) ?? '',
            'tipeBarang' => (string) self::orDefault($bb['tipeBarang'] ?? null, ''),
            'ukuranBarang' => (string) self::orDefault($bb['ukuranBarang'] ?? null, ''),
            'uraianBarang' => (string) ($bb['uraianBarang'] ?? ''),
            'bahanBakuTarif' => [],
        ];
    }

    // Angka: fallback bila kosong/0, selain itu float apa adanya.
    private function num($value, $default)
    {
        $value = self::orDefault($value, $default);

        return $value === $default ? $default : (float) $value;
    }

    // Angka dibulatkan 2 desimal: fallback bila kosong/0.
    private function round2($value, $default)
    {
        $value = self::orDefault($value, $default);

        return $value === $default ? $default : round((float) $value, 2);
    }

    // Float dibulatkan 2 desimal (skema: kelipatan 0.01); 0.0 bila kosong.
    private function flt2($value): float
    {
        return round((float) self::orDefault($value, 0), 2);
    }

    // Berat (netto) dibulatkan 4 desimal (skema: kelipatan 0.0001, jangan dibulatkan ke 2 desimal); 0.0 bila kosong.
    private function flt4($value): float
    {
        return round((float) self::orDefault($value, 0), 4);
    }

    private function strOrNull($value): ?string
    {
        return $value === null ? null : (string) $value;
    }

    // 'Y-m-d' atau null. Menerima juga 'Y-m-d H:i:s(.v)' dari SQL Server.
    private function date($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = substr((string) $value, 0, 10);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }
}
