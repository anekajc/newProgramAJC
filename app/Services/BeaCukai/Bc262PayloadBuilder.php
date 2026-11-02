<?php

namespace App\Services\BeaCukai;

use App\Services\BeaCukai\Concerns\ValutaIso;

// Penyusun JSON CEISA untuk BC 2.6.2. Awalnya salinan pengirim Villiger (docs/documentbc262_plan.md §9), lalu
// disesuaikan dengan skema resmi docs/bc_schema/bc262.json (docs/bc_schema/bc_update/bc262_update.md):
//   - key yang tidak ada di skema dibuang: root (jumlahKontainer, tempatStuffing, tglAkhirBerlaku, tglAwalBerlaku,
//     totalDanaSawit), barang (idBarang, diskon, freight, hargaSatuan, kodeKategoriBarang, kodePerhitungan, barangTarif,
//     nilaiDiskon, volume, persenBM/PPN/PPh, barangDokumen), entitas (kodeNegara Pemilik, kodeJenisApi + izin Pengirim),
//     pengangkut (kodeBendera, nama, nomor), kontainer (merkKontainer);
//   - entitas slot tetap E1->seri 3, E2->seri 7, E3->seri 9 (slot kosong tidak menggeser yang lain);
//   - pungutan = satu objek per baris di tab Pungutan (jenis + fasilitas + nilai pilihan user); tanpa baris = [];
//   - bahanBakuTarif kosong; nomorAjuDokAsal bahan baku = nomor AJU BC 2.6.1 sumber (header['nomorAjuSumber']);
//   - kodeValuta diterjemahkan ke kode ISO (config beacukai.valuta_iso); nilai dibulatkan sesuai multipleOf skema
//     (cif / nilaiBarang 2 desimal, bruto / netto / jumlahSatuan 4 desimal);
//   - teks kosong dikirim "" (bukan null) supaya pemeriksa skema melaporkannya sebagai "wajib diisi".
// CEISA menolak payload yang urutan key-nya berbeda, jadi setiap objek disusun manual. Jangan pakai
// array_merge/toArray untuk menyusunnya. Sengaja dipisah dari Bc261PayloadBuilder (bahan baku, slot entitas 9,
// kodeTujuanPemasukan dan pungutan berbeda) supaya perubahan di satu dokumen tidak mengubah dokumen lain.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase yang nilainya sudah bertipe PHP -
// desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null. Konversi dari
// baris SQL Server dikerjakan controller.
class Bc262PayloadBuilder
{
    use ValutaIso;

    /**
     * @param  array  $header  header dokumen (§2.1) + 'nomorAjuSumber' (nomor AJU BC 2.6.1 sumber; '' = standalone)
     * @param  array  $children  ['barang' => [...], 'bahanbaku' => [...], 'kemasan' => [...], 'kontainer' => [...], 'jaminan' => [...], 'dokumen' => [...], 'pungutan' => [...]]
     * @param  array  $entitas  [entitas 1 (Pengusaha TPB), 2 (Pemilik Barang), 3 (Pengirim Barang)] dari salinan di dokumen; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));
        $bahanBaku = array_values($children['bahanbaku'] ?? []);
        $nomorAjuSumber = trim((string) ($header['nomorAjuSumber'] ?? ''));
        $cif = $this->dec($header['cif'] ?? null, 2);

        return [
            'asalData' => 'S',
            'asuransi' => 0,
            'biayaTambahan' => 0,
            'biayaPengurang' => 0,
            'bruto' => $this->dec($header['bruto'] ?? null, 4),
            'cif' => $cif,
            'disclaimer' => '1',
            'freight' => 0,
            'hargaPenyerahan' => $cif,
            'jabatanTtd' => (string) ($header['jabatanTtd'] ?? ''),
            'kodeDokumen' => '262',
            'kodeKantor' => (string) ($header['kodeKantor'] ?? ''),
            'kodeTujuanPemasukan' => (string) ($header['kodeTujuanPemasukan'] ?? ''),
            'kodeValuta' => $this->valutaIso((string) self::orDefault($header['kodeValuta'] ?? null, '')),
            'kotaTtd' => (string) ($header['kotaTtd'] ?? ''),
            'namaTtd' => (string) ($header['namaTtd'] ?? ''),
            'ndpbm' => $this->dec($header['ndpbm'] ?? null, 4),
            'netto' => $this->dec($header['netto'] ?? null, 4),
            'nik' => '0',
            'nilaiBarang' => $cif,
            'nomorAju' => (string) ($header['nomorAju'] ?? ''),
            'seri' => 1,
            'tanggalAju' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'tanggalTtd' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'barang' => array_map(fn ($row) => $this->barang($row, $bahanBaku, $nomorAjuSumber), $barang),
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'entitas' => $this->entitas($entitas),
            'jaminan' => array_map(fn ($row) => $this->jaminan($row), array_values($children['jaminan'] ?? [])),
            'kemasan' => array_map(fn ($row) => $this->kemasan($row), array_values($children['kemasan'] ?? [])),
            'kontainer' => array_map(fn ($row) => $this->kontainer($row), array_values($children['kontainer'] ?? [])),
            'pengangkut' => [[
                'idPengangkut' => '1',
                'kodeCaraAngkut' => (string) self::orDefault($header['kodeCaraAngkut'] ?? null, ''),
                'seriPengangkut' => 1,
            ]],
            'pungutan' => $this->pungutan(array_values($children['pungutan'] ?? [])),
            'uangMuka' => 0,
            'vd' => 0,
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

    // Satu objek per baris tab Pungutan, urut baris; idPungutan = nomor baris. Tanpa baris = [].
    private function pungutan(array $rows): array
    {
        $out = [];
        foreach ($rows as $i => $row) {
            $out[] = [
                'idPungutan' => (string) ($i + 1),
                'kodeFasilitasTarif' => (string) ($row['kodeFasilitasTarif'] ?? ''),
                'kodeJenisPungutan' => (string) ($row['kodeJenisPungutan'] ?? ''),
                'nilaiPungutan' => $this->dec($row['nilaiPungutan'] ?? null, 2),
            ];
        }

        return $out;
    }

    // Slot tetap seri 3 (Pengusaha TPB) -> 7 (Pemilik Barang) -> 9 (Pengirim Barang), mengikuti skema resmi:
    // hanya Pengusaha yang punya NIB / jenis API / izin; Pemilik dan Pengirim hanya data identitas.
    private function entitas(array $entitas): array
    {
        $e = fn (int $i, string $key, $default = '') => ! empty($entitas[$i]) ? self::orDefault($entitas[$i][$key] ?? null, $default) : $default;

        return [
            [
                'alamatEntitas' => (string) $e(0, 'alamatEntitas'),
                'kodeEntitas' => '3',
                'kodeJenisApi' => (string) $e(0, 'kodeJenisApi', '02'),
                'kodeJenisIdentitas' => (string) $e(0, 'kodeJenisIdentitas', '6'),
                'namaEntitas' => (string) $e(0, 'namaEntitas'),
                'kodeStatus' => (string) $e(0, 'kodeStatus', '0'),
                'nibEntitas' => (string) $e(0, 'nibEntitas'),
                'nomorIdentitas' => (string) $e(0, 'nomorIdentitas'),
                'nomorIjinEntitas' => (string) $e(0, 'nomorIjinEntitas'),
                'tanggalIjinEntitas' => ! empty($entitas[0]) ? ($this->date($entitas[0]['tanggalIjinEntitas'] ?? null) ?? '') : '',
                'seriEntitas' => 3,
            ],
            [
                'alamatEntitas' => (string) $e(1, 'alamatEntitas'),
                'kodeEntitas' => '7',
                'nomorIdentitas' => (string) $e(1, 'nomorIdentitas'),
                'kodeStatus' => (string) $e(1, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(1, 'namaEntitas'),
                'kodeJenisIdentitas' => (string) $e(1, 'kodeJenisIdentitas', '6'),
                'seriEntitas' => 7,
            ],
            [
                'alamatEntitas' => (string) $e(2, 'alamatEntitas'),
                'kodeEntitas' => '9',
                'kodeJenisIdentitas' => (string) $e(2, 'kodeJenisIdentitas', '6'),
                'kodeStatus' => (string) $e(2, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(2, 'namaEntitas'),
                'nomorIdentitas' => (string) $e(2, 'nomorIdentitas'),
                'seriEntitas' => 9,
            ],
        ];
    }

    // Skema: nomor dan tanggal ikut wajib bila seriDokumen dikirim; kosong dikirim "" supaya terbaca "wajib diisi".
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

    // idJaminan memang diisi kode jenis jaminan; semua kolom wajib (skema) jadi kosong dikirim "".
    private function jaminan(array $row): array
    {
        $nilai = $row['nilaiJaminan'] ?? null;

        return [
            'idJaminan' => (string) ($row['kodeJenisJaminan'] ?? ''),
            'kodeJenisJaminan' => (string) ($row['kodeJenisJaminan'] ?? ''),
            'nilaiJaminan' => $nilai === null ? null : (int) $nilai,
            'nomorBpj' => (string) ($row['nomorBpj'] ?? ''),
            'nomorJaminan' => (string) ($row['nomorJaminan'] ?? ''),
            'penjamin' => (string) ($row['penjamin'] ?? ''),
            'tanggalBpj' => $this->date($row['tanggalBpj'] ?? null) ?? '',
            'tanggalJaminan' => $this->date($row['tanggalJaminan'] ?? null) ?? '',
            'tanggalJatuhTempo' => $this->date($row['tanggalJatuhTempo'] ?? null) ?? '',
        ];
    }

    // seriKemasan dikirim sebagai angka (skema: integer); fallback 1.
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
            'kodeJenisKontainer' => (string) ($row['kodeJenisKontainer'] ?? ''),
            'kodeTipeKontainer' => (string) ($row['kodeTipeKontainer'] ?? ''),
            'kodeUkuranKontainer' => (string) ($row['kodeUkuranKontainer'] ?? ''),
            'nomorKontainer' => (string) ($row['nomorKontainer'] ?? ''),
            'seriKontainer' => (int) ($row['seriKontainer'] ?? 0),
        ];
    }

    private function barang(array $row, array $semuaBahanBaku, string $nomorAjuSumber): array
    {
        $seri = (int) ($row['seriBarang'] ?? 0);
        $bahanBaku = array_values(array_filter($semuaBahanBaku, fn ($bb) => (int) ($bb['seriBarang'] ?? 0) === $seri));
        $cifRupiah = $this->dec($row['cifRupiah'] ?? null, 2);

        return [
            'cif' => (float) ($row['cif'] ?? 0),
            'cifRupiah' => $cifRupiah,
            'hargaEkspor' => $this->dec($row['hargaEkspor'] ?? null, 2),
            'hargaPenyerahan' => $this->dec($row['hargaPenyerahan'] ?? null, 4),
            'isiPerKemasan' => $this->dec($row['isiPerKemasan'] ?? null, 2),
            'jumlahKemasan' => $this->dec($row['jumlahKemasan'] ?? null, 2),
            'jumlahSatuan' => $this->dec($row['jumlahSatuan'] ?? null, 4),
            'kodeAsalBahanBaku' => '0',
            'kodeAsalBarang' => (string) ($row['kodeAsalBarang'] ?? ''),
            'kodeBarang' => (string) ($row['kodeBarang'] ?? ''),
            'kodeDokumen' => '262',
            'kodeJenisKemasan' => (string) ($row['kodeJenisKemasan'] ?? ''),
            'kodeNegaraAsal' => (string) ($row['kodeNegaraAsal'] ?? ''),
            'kodeSatuanBarang' => (string) ($row['kodeSatuanBarang'] ?? ''),
            'merk' => (string) self::orDefault($row['merk'] ?? null, '-'),
            'ndpbm' => $this->dec($row['ndpbm'] ?? null, 4),
            'netto' => $this->dec($row['netto'] ?? null, 4),
            'nilaiBarang' => $cifRupiah,
            'nilaiJasa' => $cifRupiah,
            'posTarif' => (string) ($row['posTarif'] ?? ''),
            'seriBarang' => $seri,
            'spesifikasiLain' => (string) self::orDefault($row['spesifikasiLain'] ?? null, '0'),
            'hargaPerolehan' => $this->dec($row['hargaPerolehan'] ?? null, 4),
            'tipe' => (string) self::orDefault($row['tipe'] ?? null, '0'),
            'uangMuka' => 0,
            'ukuran' => (string) self::orDefault($row['ukuran'] ?? null, '0'),
            'uraian' => (string) ($row['uraian'] ?? ''),
            'bahanBaku' => array_map(fn ($bb) => $this->bahanBaku($bb, $nomorAjuSumber), $bahanBaku),
        ];
    }

    // 28 key persis skema (tanpa flagTis / isiPerKemasan / nomorDokumen). seriBarangDokAsal = isian user (1 bila
    // kosong); bahanBakuTarif dikirim kosong (keputusan user, seperti BC 2.5 dan 2.6.1); nomorAjuDokAsal = nomor AJU
    // BC 2.6.1 sumber ("" bila dokumen berdiri sendiri - dilaporkan saat kirim).
    private function bahanBaku(array $bb, string $nomorAjuSumber): array
    {
        return [
            'cif' => $this->dec($bb['cif'] ?? null, 4),
            'cifRupiah' => $this->dec($bb['cifRupiah'] ?? null, 2),
            'hargaPenyerahan' => $this->dec($bb['hargaPenyerahan'] ?? null, 4),
            'hargaPerolehan' => $this->dec($bb['hargaPerolehan'] ?? null, 4),
            'jumlahSatuan' => $this->dec($bb['jumlahSatuan'] ?? null, 4),
            'kodeSatuanBarang' => (string) ($bb['kodeSatuanBarang'] ?? ''),
            'kodeAsalBahanBaku' => (string) self::orDefault($bb['kodeAsalBahanBaku'] ?? null, '0'),
            'kodeBarang' => (string) ($bb['kodeBarang'] ?? ''),
            'kodeDokAsal' => (string) ($bb['kodeDokumen'] ?? ''),
            'kodeDokumen' => '262',
            'kodeKantor' => (string) ($bb['kodeKantorAsal'] ?? ''),
            'merkBarang' => (string) self::orDefault($bb['merkBarang'] ?? null, ''),
            'ndpbm' => $this->dec($bb['ndpbm'] ?? null, 4),
            'netto' => $this->dec($bb['netto'] ?? null, 4),
            'nomorAjuDokAsal' => $nomorAjuSumber,
            'nomorDaftarDokAsal' => (string) ($bb['nomorDaftarDokAsal'] ?? ''),
            'posTarif' => (string) ($bb['posTarif'] ?? ''),
            'seriBahanBaku' => (int) ($bb['seriBahanBaku'] ?? 0),
            'seriBarang' => (int) ($bb['seriBarang'] ?? 0),
            'seriBarangDokAsal' => (int) self::orDefault($bb['seriBarangDokAsal'] ?? null, 1),
            'seriIjin' => (int) self::orDefault($bb['seriIjin'] ?? null, 1),
            'spesifikasiLainBarang' => (string) self::orDefault($bb['spesifikasiLainBarang'] ?? null, ''),
            'tanggalDaftarDokAsal' => $this->date($bb['tanggalDaftarDokAsal'] ?? null) ?? '',
            'tipeBarang' => (string) self::orDefault($bb['tipeBarang'] ?? null, ''),
            'ukuranBarang' => (string) self::orDefault($bb['ukuranBarang'] ?? null, ''),
            'uraianBarang' => (string) ($bb['uraianBarang'] ?? ''),
            'nilaiJasa' => $this->dec($bb['nilaiJasa'] ?? null, 4),
            'bahanBakuTarif' => [],
        ];
    }

    // Float dibulatkan $skala desimal (skema: multipleOf); 0.0 bila kosong.
    private function dec($value, int $skala): float
    {
        return round((float) self::orDefault($value, 0), $skala);
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
