<?php

namespace App\Services\BeaCukai;

use Illuminate\Container\Container;

// Penyusun JSON CEISA untuk BC 4.1 mengikuti skema RESMI docs/bc_schema/bc41.json (bukan lagi daftar key Villiger
// di docs/documentbc41_plan.md §9). Setiap objek disusun manual dengan urutan key skema; jangan pakai
// array_merge/toArray untuk menyusunnya. Sengaja dipisah dari builder BC lain supaya perubahan di satu dokumen
// tidak mengubah dokumen lain.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase yang nilainya sudah bertipe PHP -
// desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null. Konversi dari
// baris SQL Server dikerjakan controller. userPortal dikirim lewat $header['userPortal'] (username CEISA dari
// login terakhir, kosong bila belum login).
//
// Beda dari plan Villiger (disepakati dengan user, 29 Oktober 2026):
//   - key yang TIDAK ada di skema dihapus: root disclaimer / kodeValuta / jaminan / pungutan / nomorBuktiBayar /
//     tanggalBuktiBayar; barang idBarang / diskon / freight / hargaSatuan / kodeAsalBarang / kodeKategoriBarang /
//     kodeNegaraAsal / kodePerhitungan / nilaiDiskon / barangTarif / barangDokumen; pengangkut kodeBendera /
//     kodeCaraAngkut; key tambahan di entitas
//   - key wajib yang dulu hilang: root userPortal, volume; barang volume; Pemilik (seri 7) kodeJenisApi +
//     nomorIjinEntitas + tanggalIjinEntitas; pengangkut diisi nama / nomor, seriPengangkut string, idPengangkut angka
//   - entitas tetap E1→seri 3, E2→seri 7, E3→seri 8, ketiganya wajib (slot kosong tetap dikirim dan dicatat saat kirim)
//   - pembulatan sesuai multipleOf skema: cif / nilaiBarang / PPN / PPnBM 2 desimal, netto / bruto / volume 4 desimal,
//     bahan baku cif / harga 2 desimal; nilaiJasa bahan baku tetap angka (hingga 2 desimal, bukan integer)
//   - tanggalAju = tanggalTtd (sama dengan BC 2.5)
class Bc41PayloadBuilder
{
    /**
     * @param  array  $header  header dokumen (§2.1), plus 'userPortal'
     * @param  array  $children  ['barang' => [...], 'bahanbaku' => [...], 'kemasan' => [...], 'kontainer' => [...], 'dokumen' => [...]]
     * @param  array  $entitas  [entitas 1 (Pengusaha TPB), 2 (Pemilik Barang), 3 (Penerima Barang)] dari salinan di dokumen; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));
        $bahanBaku = array_values($children['bahanbaku'] ?? []);
        $kontainer = array_values($children['kontainer'] ?? []);
        $hargaPenyerahan = $this->dec($header['hargaPenyerahan'] ?? null, 4);
        $tanggalTtd = $this->date($header['tanggalTtd'] ?? null) ?? '';

        return [
            'asalData' => 'S',
            'asuransi' => 0.0,
            'bruto' => $this->dec($header['bruto'] ?? null, 4),
            'cif' => $this->dec($header['cif'] ?? null, 2),
            'dasarPengenaanPajak' => $this->dec($header['dasarPengenaanPajak'] ?? null, 2),
            'kodeJenisTpb' => (string) self::orDefault($header['kodeJenisTpb'] ?? null, '1'),
            'freight' => 0.0,
            'hargaPenyerahan' => $hargaPenyerahan,
            'jabatanTtd' => (string) ($header['jabatanTtd'] ?? ''),
            'jumlahKontainer' => count($kontainer),
            'kodeDokumen' => '41',
            'kodeKantor' => (string) ($header['kodeKantor'] ?? ''),
            'kodeLokasiBayar' => (string) $this->cfg('kode_lokasi_bayar', '1'),
            'kodePembayar' => (string) self::orDefault($header['kodePembayar'] ?? null, ''),
            'kodeTujuanPengiriman' => (string) ($header['kodeTujuanPengiriman'] ?? ''),
            'kotaTtd' => (string) ($header['kotaTtd'] ?? ''),
            'namaTtd' => (string) ($header['namaTtd'] ?? ''),
            'ndpbm' => $this->dec($header['ndpbm'] ?? null, 4),
            'netto' => $this->dec($header['netto'] ?? null, 4),
            'nik' => '0',
            'nilaiBarang' => round($hargaPenyerahan, 2),
            'nomorAju' => (string) ($header['nomorAju'] ?? ''),
            'seri' => 1,
            'tanggalAju' => $tanggalTtd,
            'tanggalTtd' => $tanggalTtd,
            'userPortal' => (string) ($header['userPortal'] ?? ''),
            'volume' => $this->dec($header['volume'] ?? null, 4),
            'biayaTambahan' => 0.0,
            'biayaPengurang' => $this->dec($header['diskon'] ?? null, 4),
            'vd' => 0.0,
            'uangMuka' => $this->dec($header['uangMuka'] ?? null, 4),
            'ppnPajak' => $this->dec($header['ppnPajak'] ?? null, 2),
            'ppnbmPajak' => $this->dec($header['ppnbmPajak'] ?? null, 2),
            'tarifPpnPajak' => $this->dec($header['tarifPpnPajak'] ?? null, 2),
            'tarifPpnbmPajak' => $this->dec($header['tarifPpnbmPajak'] ?? null, 2),
            'entitas' => $this->entitas($entitas),
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'pengangkut' => [[
                'idPengangkut' => 1,
                'namaPengangkut' => (string) ($header['namaPengangkut'] ?? ''),
                'nomorPengangkut' => (string) ($header['nomorPengangkut'] ?? ''),
                'seriPengangkut' => '1',
            ]],
            'kontainer' => array_map(fn ($row) => $this->kontainer($row), $kontainer),
            'kemasan' => array_map(fn ($row) => $this->kemasan($row), array_values($children['kemasan'] ?? [])),
            'barang' => array_map(fn ($row) => $this->barang($row, $bahanBaku), $barang),
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

    // Konstanta dari config/beacukai.php (bc41.*); bawaan Villiger bila container belum punya config
    // (mis. dipakai di luar aplikasi).
    private function cfg(string $key, $default)
    {
        $container = Container::getInstance();

        return $container->bound('config') ? $container->make('config')->get("beacukai.bc41.{$key}", $default) : $default;
    }

    // Skema resmi: slot tetap seri 3 (Pengusaha TPB) → 7 (Pemilik Barang) → 8 (Penerima Barang). Slot kosong tetap
    // dikirim dengan nilai kosong (dan nilai bawaan jenis API / identitas / status) supaya masuk daftar masalah.
    private function entitas(array $entitas): array
    {
        $e = fn (int $i, string $key, $default = '') => ! empty($entitas[$i]) ? self::orDefault($entitas[$i][$key] ?? null, $default) : $default;
        $tgl = fn (int $i) => ! empty($entitas[$i]) ? ($this->date($entitas[$i]['tanggalIjinEntitas'] ?? null) ?? '') : '';

        return [
            [
                'alamatEntitas' => (string) $e(0, 'alamatEntitas'),
                'kodeEntitas' => '3',
                'kodeJenisIdentitas' => (string) $e(0, 'kodeJenisIdentitas', '6'),
                'namaEntitas' => (string) $e(0, 'namaEntitas'),
                'nibEntitas' => (string) $e(0, 'nibEntitas'),
                'nomorIdentitas' => (string) $e(0, 'nomorIdentitas'),
                'nomorIjinEntitas' => (string) $e(0, 'nomorIjinEntitas'),
                'seriEntitas' => 3,
                'tanggalIjinEntitas' => $tgl(0),
            ],
            [
                'alamatEntitas' => (string) $e(1, 'alamatEntitas'),
                'kodeEntitas' => '7',
                'kodeJenisApi' => (string) $e(1, 'kodeJenisApi', '02'),
                'kodeJenisIdentitas' => (string) $e(1, 'kodeJenisIdentitas', '6'),
                'kodeStatus' => (string) $e(1, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(1, 'namaEntitas'),
                'nomorIdentitas' => (string) $e(1, 'nomorIdentitas'),
                'nomorIjinEntitas' => (string) $e(1, 'nomorIjinEntitas'),
                'seriEntitas' => 7,
                'tanggalIjinEntitas' => $tgl(1),
            ],
            [
                'alamatEntitas' => (string) $e(2, 'alamatEntitas'),
                'kodeEntitas' => '8',
                'kodeJenisApi' => (string) $e(2, 'kodeJenisApi', '02'),
                'kodeJenisIdentitas' => (string) $e(2, 'kodeJenisIdentitas', '6'),
                'kodeStatus' => (string) $e(2, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(2, 'namaEntitas'),
                'nomorIdentitas' => (string) $e(2, 'nomorIdentitas'),
                'seriEntitas' => 8,
            ],
        ];
    }

    // Nomor / tanggal kosong dikirim "" supaya tercatat satu kali sebagai "wajib diisi" saat kirim (daftar wajibIsi).
    private function dokumen(array $row): array
    {
        return [
            'idDokumen' => '0',
            'kodeDokumen' => (string) ($row['kodeDokumen'] ?? ''),
            'nomorDokumen' => (string) ($row['nomorDokumen'] ?? ''),
            'seriDokumen' => (int) ($row['seriDokumen'] ?? 0),
            'tanggalDokumen' => $this->date($row['tanggalDokumen'] ?? null) ?? '',
        ];
    }

    // jumlahKemasan 0 tetap 0 (ditolak oleh aturan tambahan controller), bukan null.
    private function kemasan(array $row): array
    {
        return [
            'jumlahKemasan' => (int) self::orDefault($row['jumlahKemasan'] ?? null, 0),
            'kodeJenisKemasan' => (string) self::orDefault($row['kodeJenisKemasan'] ?? null, ''),
            'merkKemasan' => (string) self::orDefault($row['merkKemasan'] ?? null, ''),
            'seriKemasan' => (int) self::orDefault($row['seriKemasan'] ?? null, 1),
        ];
    }

    // Tanpa merkKontainer (beda dengan BC 2.6.2).
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

    // 27 key sesuai skema resmi (tanpa barangTarif / barangDokumen / idBarang / negara asal / kategori ...).
    private function barang(array $row, array $semuaBahanBaku): array
    {
        $seri = (int) ($row['seriBarang'] ?? 0);
        $bahanBaku = array_values(array_filter($semuaBahanBaku, fn ($bb) => (int) ($bb['seriBarang'] ?? 0) === $seri));
        $cifRupiah = $row['cifRupiah'] ?? null;

        return [
            'cif' => (float) ($row['cif'] ?? 0),
            'hargaEkspor' => $this->num($row['hargaEkspor'] ?? null, 0),
            'hargaPenyerahan' => $this->num($row['hargaPenyerahan'] ?? null, 0),
            'isiPerKemasan' => $this->dec($row['isiPerKemasan'] ?? null, 2),
            'jumlahKemasan' => $this->dec($row['jumlahKemasan'] ?? null, 2),
            'jumlahSatuan' => $this->dec($row['jumlahSatuan'] ?? null, 4),
            'kodeBarang' => (string) ($row['kodeBarang'] ?? ''),
            'kodeDokumen' => '41',
            'kodeJenisKemasan' => (string) ($row['kodeJenisKemasan'] ?? ''),
            'kodeSatuanBarang' => (string) ($row['kodeSatuanBarang'] ?? ''),
            'merk' => (string) self::orDefault($row['merk'] ?? null, '-'),
            'netto' => $this->dec($row['netto'] ?? null, 4),
            'nilaiBarang' => $this->dec($row['nilaiBarang'] ?? null, 2),
            'posTarif' => (string) ($row['posTarif'] ?? ''),
            'seriBarang' => $seri,
            'spesifikasiLain' => (string) self::orDefault($row['spesifikasiLain'] ?? null, '0'),
            'tipe' => (string) self::orDefault($row['tipe'] ?? null, '0'),
            'ukuran' => (string) self::orDefault($row['ukuran'] ?? null, '0'),
            'uraian' => (string) ($row['uraian'] ?? ''),
            'volume' => $this->dec($row['volume'] ?? null, 4),
            'cifRupiah' => round((float) ($cifRupiah ?? 0), 2),
            'hargaPerolehan' => $this->num($row['hargaPerolehan'] ?? null, 0),
            'kodeAsalBahanBaku' => '0',
            'ndpbm' => $this->num($row['ndpbm'] ?? null, 0),
            'uangMuka' => 0,
            'nilaiJasa' => round((float) ($cifRupiah ?? 0), 2),
            'bahanBaku' => array_map(fn ($bb) => $this->bahanBaku($bb), $bahanBaku),
        ];
    }

    // 25 key sesuai skema. kodeAsalBahanBaku selalu "0" (tidak ada isian asal). nilaiJasa di skema bertipe integer,
    // tetapi keputusan user: tetap angka (hingga 2 desimal) - pemeriksa skema memakai opsi 'angka' untuk jalur ini.
    private function bahanBaku(array $bb): array
    {
        return [
            'cif' => $this->dec($bb['cif'] ?? null, 2),
            'cifRupiah' => $this->dec($bb['cifRupiah'] ?? null, 2),
            'hargaPenyerahan' => $this->dec($bb['hargaPenyerahan'] ?? null, 2),
            'hargaPerolehan' => $this->dec($bb['hargaPerolehan'] ?? null, 2),
            'jumlahSatuan' => $this->dec($bb['jumlahSatuan'] ?? null, 4),
            'kodeSatuanBarang' => (string) ($bb['kodeSatuanBarang'] ?? ''),
            'kodeAsalBahanBaku' => '0',
            'kodeBarang' => (string) ($bb['kodeBarang'] ?? ''),
            'kodeDokAsal' => (string) ($bb['kodeDokumen'] ?? ''),
            'kodeDokumen' => '41',
            'kodeKantor' => (string) ($bb['kodeKantorAsal'] ?? ''),
            'merkBarang' => (string) self::orDefault($bb['merkBarang'] ?? null, ''),
            'ndpbm' => 0,
            'netto' => $this->dec($bb['netto'] ?? null, 4),
            'nomorAjuDokAsal' => $this->strOrNull($bb['nomorAjuDokAsal'] ?? null),
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
            'nilaiJasa' => $this->dec($bb['nilaiJasa'] ?? null, 2),
        ];
    }

    // Selalu float dibulatkan $skala desimal (0.0 bila kosong).
    private function dec($value, int $skala): float
    {
        return round((float) self::orDefault($value, 0), $skala);
    }

    // Angka tanpa batas skema: fallback bila kosong/0, selain itu float apa adanya.
    private function num($value, $default)
    {
        $value = self::orDefault($value, $default);

        return $value === $default ? $default : (float) $value;
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
