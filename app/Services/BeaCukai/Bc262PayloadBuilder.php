<?php

namespace App\Services\BeaCukai;

// Penyusun JSON CEISA untuk BC 2.6.2 - spesifikasi docs/documentbc262_plan.md §9. CEISA menolak payload
// yang urutan key-nya berbeda, jadi setiap objek disusun manual dengan urutan key yang sama dengan
// tabel di §9. Jangan pakai array_merge/toArray untuk menyusunnya. Sengaja dipisah dari
// Bc261PayloadBuilder (bahan baku, slot entitas 9, kodeTujuanPemasukan, array kontainer dan tipe
// seriKemasan berbeda) supaya perubahan di satu dokumen tidak mengubah urutan dokumen lain.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase yang nilainya sudah bertipe PHP -
// desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null. Konversi dari
// baris SQL Server dikerjakan controller.
//
// Perbedaan dari Villiger yang SENGAJA diperbaiki (disepakati dengan user):
//   Q-d  entitas dipetakan tetap E1→seri 3, E2→seri 7, E3→seri 9 (slot kosong berisi nilai bawaan)
//   Q-f  data entitas diambil dari salinan di dokumen, bukan master
//   Q-i  seriBarangDokAsal dan bahanBakuTarif.seriBahanBaku memakai nilai tersimpan (bawaan 1)
//   Q-c  array pungutan dan barangTarif mengikuti nilai di tab Pungutan (lihat pungutan() / barangTarif())
class Bc262PayloadBuilder
{
    // Urutan jenis pungutan di array pungutan: [key header, kodeJenisPungutan CEISA].
    private const JENIS_PUNGUTAN = [
        ['nilaiBM', 'BM'],
        ['nilaiBMAD', 'BMAD'],
        ['nilaiBMI', 'BMI'],
        ['nilaiBMTP', 'BMTP'],
        ['nilaiBMP', 'BMP'],
        ['nilaiCukaiEa', 'CEA'],
        ['nilaiCukaiCmea', 'CMEA'],
        ['nilaiCukaiCtem', 'CTEM'],
        ['nilaiPPN', 'PPN'],
        ['nilaiPPNBM', 'PPNBM'],
        ['nilaiPPh', 'PPH'],
    ];

    /**
     * @param  array  $header  header dokumen (§2.1)
     * @param  array  $children  ['barang' => [...], 'bahanbaku' => [...], 'kemasan' => [...], 'kontainer' => [...], 'jaminan' => [...], 'dokumen' => [...]]
     * @param  array  $entitas  [entitas 1 (Pengusaha TPB), 2 (Pemilik Barang), 3 (Pengirim Barang)] dari salinan di dokumen; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));
        $bahanBaku = array_values($children['bahanbaku'] ?? []);
        $tarif = $this->tarifBarang($header, $barang);

        return [
            'asalData' => 'S',
            'asuransi' => 0,
            'biayaTambahan' => 0,
            'biayaPengurang' => 0,
            'bruto' => $this->num($header['bruto'] ?? null, 0),
            'cif' => $this->num($header['cif'] ?? null, 0),
            'disclaimer' => '1',
            'freight' => 0,
            'hargaPenyerahan' => $this->num($header['cif'] ?? null, 0),
            'jabatanTtd' => (string) ($header['jabatanTtd'] ?? ''),
            'jumlahKontainer' => 0,
            'kodeDokumen' => '262',
            'kodeKantor' => (string) ($header['kodeKantor'] ?? ''),
            'kodeTujuanPemasukan' => (string) ($header['kodeTujuanPemasukan'] ?? ''),
            'kodeValuta' => (string) self::orDefault($header['kodeValuta'] ?? null, ''),
            'kotaTtd' => (string) ($header['kotaTtd'] ?? ''),
            'namaTtd' => (string) ($header['namaTtd'] ?? ''),
            'ndpbm' => $this->num($header['ndpbm'] ?? null, 0),
            'netto' => $this->round2($header['netto'] ?? null, 0),
            'nik' => '0',
            'nilaiBarang' => $this->num($header['cif'] ?? null, 0),
            'nomorAju' => (string) ($header['nomorAju'] ?? ''),
            'seri' => 1,
            'tanggalAju' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'tanggalTtd' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'tempatStuffing' => '',
            'tglAkhirBerlaku' => '',
            'tglAwalBerlaku' => '',
            'totalDanaSawit' => '',
            'barang' => array_map(fn ($row, $i) => $this->barang($row, $bahanBaku, $tarif[$i]), $barang, array_keys($barang)),
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'entitas' => $this->entitas($entitas),
            'jaminan' => array_map(fn ($row) => $this->jaminan($row), array_values($children['jaminan'] ?? [])),
            'kemasan' => array_map(fn ($row) => $this->kemasan($row), array_values($children['kemasan'] ?? [])),
            'kontainer' => array_map(fn ($row) => $this->kontainer($row), array_values($children['kontainer'] ?? [])),
            'pengangkut' => [[
                'idPengangkut' => '1',
                'kodeBendera' => '',
                'namaPengangkut' => '',
                'nomorPengangkut' => '',
                'kodeCaraAngkut' => (string) self::orDefault($header['kodeCaraAngkut'] ?? null, ''),
                'seriPengangkut' => 1,
            ]],
            'pungutan' => $this->pungutan($header, $barang),
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

    // True bila salah satu dari 11 jenis pungutan di tab Pungutan terisi (> 0).
    private function adaPungutan(array $header): bool
    {
        foreach (self::JENIS_PUNGUTAN as [$key]) {
            if ((float) ($header[$key] ?? 0) > 0) {
                return true;
            }
        }

        return false;
    }

    // §9.7b (diubah - Q-c): satu objek per jenis pungutan yang nilainya > 0. Bila tab Pungutan kosong
    // semua, dikirim perilaku lama Villiger: satu baris PPN senilai CIF Rupiah barang PERTAMA.
    private function pungutan(array $header, array $barang): array
    {
        if (! $this->adaPungutan($header)) {
            return [[
                'idPungutan' => '1',
                'kodeFasilitasTarif' => '3',
                'kodeJenisPungutan' => 'PPN',
                'nilaiPungutan' => $barang ? round((float) ($barang[0]['cifRupiah'] ?? 0), 2) : 0,
            ]];
        }

        $out = [];
        foreach (self::JENIS_PUNGUTAN as [$key, $kode]) {
            $nilai = (float) ($header[$key] ?? 0);
            if ($nilai > 0) {
                $out[] = [
                    'idPungutan' => (string) (count($out) + 1),
                    'kodeFasilitasTarif' => '3',
                    'kodeJenisPungutan' => $kode,
                    'nilaiPungutan' => round($nilai, 2),
                ];
            }
        }

        return $out;
    }

    // nilaiBayar BM / PPH / PPN per barang. Bila tab Pungutan terisi: bagian tiap barang dari nilai
    // header, sebanding CIF Rupiah barang (sama rata bila semua CIF Rupiah 0); barang terakhir
    // menampung sisa pembulatan sehingga jumlahnya persis nilai header. Bila kosong: perilaku lama
    // (CIF Rupiah penuh). Hasil: [index barang => ['BM' => .., 'PPH' => .., 'PPN' => .., 'dariPungutan' => bool]].
    private function tarifBarang(array $header, array $barang): array
    {
        $pakai = $this->adaPungutan($header);
        $hasil = [];
        foreach ($barang as $i => $row) {
            $hasil[$i] = ['dariPungutan' => $pakai];
        }
        foreach ([['BM', 'nilaiBM'], ['PPH', 'nilaiPPh'], ['PPN', 'nilaiPPN']] as [$jenis, $key]) {
            $bagi = $pakai
                ? $this->bagi((float) ($header[$key] ?? 0), array_map(fn ($r) => (float) ($r['cifRupiah'] ?? 0), $barang))
                : array_map(fn ($r) => $this->round2($r['cifRupiah'] ?? null, 0), $barang);
            foreach ($barang as $i => $row) {
                $hasil[$i][$jenis] = $bagi[$i];
            }
        }

        return $hasil;
    }

    // Bagi $total ke tiap bobot (dibulatkan 2 desimal, sisa ke elemen terakhir).
    private function bagi(float $total, array $bobot): array
    {
        $n = count($bobot);
        if ($n === 0) {
            return [];
        }
        $jumlah = array_sum($bobot);
        $out = [];
        $terbagi = 0.0;
        foreach (array_values($bobot) as $i => $b) {
            if ($i === $n - 1) {
                $out[$i] = round($total - $terbagi, 2);
            } else {
                $bagian = round($jumlah > 0 ? $total * $b / $jumlah : $total / $n, 2);
                $out[$i] = $bagian;
                $terbagi += $bagian;
            }
        }

        return $out;
    }

    // §9.2 (Q-d): slot tetap seri 3 (Pengusaha TPB) → 7 (Pemilik Barang) → 9 (Pengirim Barang).
    private function entitas(array $entitas): array
    {
        $e = fn (int $i, string $key, $default = '') => isset($entitas[$i]) ? self::orDefault($entitas[$i][$key] ?? null, $default) : $default;
        $tgl = fn (int $i) => isset($entitas[$i]) ? ($this->date($entitas[$i]['tanggalIjinEntitas'] ?? null) ?? '') : '';

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
                'tanggalIjinEntitas' => $tgl(0),
                'seriEntitas' => 3,
            ],
            [
                'alamatEntitas' => (string) $e(1, 'alamatEntitas'),
                'kodeEntitas' => '7',
                'nomorIdentitas' => (string) $e(1, 'nomorIdentitas'),
                'kodeNegara' => (string) $e(1, 'kodeNegara'),
                'kodeStatus' => (string) $e(1, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(1, 'namaEntitas'),
                'kodeJenisIdentitas' => (string) $e(1, 'kodeJenisIdentitas', '6'),
                'seriEntitas' => 7,
            ],
            [
                'alamatEntitas' => (string) $e(2, 'alamatEntitas'),
                'kodeEntitas' => '9',
                'kodeJenisApi' => (string) $e(2, 'kodeJenisApi', '02'),
                'kodeJenisIdentitas' => (string) $e(2, 'kodeJenisIdentitas', '6'),
                'kodeStatus' => (string) $e(2, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(2, 'namaEntitas'),
                'nomorIdentitas' => (string) $e(2, 'nomorIdentitas'),
                'nomorIjinEntitas' => (string) $e(2, 'nomorIjinEntitas'),
                'tanggalIjinEntitas' => $tgl(2),
                'seriEntitas' => 9,
            ],
        ];
    }

    // §9.3
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

    // §9.4 - idJaminan memang diisi kode jenis jaminan.
    private function jaminan(array $row): array
    {
        $nilai = $row['nilaiJaminan'] ?? null;

        return [
            'idJaminan' => $this->strOrNull($row['kodeJenisJaminan'] ?? null),
            'kodeJenisJaminan' => $this->strOrNull($row['kodeJenisJaminan'] ?? null),
            'nilaiJaminan' => $nilai === null ? null : (int) $nilai,
            'nomorBpj' => $this->strOrNull($row['nomorBpj'] ?? null),
            'nomorJaminan' => $this->strOrNull($row['nomorJaminan'] ?? null),
            'penjamin' => $this->strOrNull($row['penjamin'] ?? null),
            'tanggalBpj' => $this->date($row['tanggalBpj'] ?? null),
            'tanggalJaminan' => $this->date($row['tanggalJaminan'] ?? null),
            'tanggalJatuhTempo' => $this->date($row['tanggalJatuhTempo'] ?? null),
        ];
    }

    // §9.5 - seriKemasan dikirim sebagai angka (BC 2.6.1: teks); fallback 1.
    private function kemasan(array $row): array
    {
        return [
            'jumlahKemasan' => (int) self::orDefault($row['jumlahKemasan'] ?? null, 0),
            'kodeJenisKemasan' => (string) self::orDefault($row['kodeJenisKemasan'] ?? null, ''),
            'seriKemasan' => (int) self::orDefault($row['seriKemasan'] ?? null, 1),
            'merkKemasan' => (string) self::orDefault($row['merkKemasan'] ?? null, ''),
        ];
    }

    // §9.6 - baru di BC 2.6.2 (BC 2.6.1 menyusunnya tetapi tidak mengirim).
    private function kontainer(array $row): array
    {
        return [
            'kodeJenisKontainer' => $this->strOrNull($row['kodeJenisKontainer'] ?? null),
            'kodeTipeKontainer' => $this->strOrNull($row['kodeTipeKontainer'] ?? null),
            'kodeUkuranKontainer' => $this->strOrNull($row['kodeUkuranKontainer'] ?? null),
            'nomorKontainer' => $this->strOrNull($row['nomorKontainer'] ?? null),
            'seriKontainer' => (int) ($row['seriKontainer'] ?? 0),
            'merkKontainer' => '',
        ];
    }

    // §9.8
    private function barang(array $row, array $semuaBahanBaku, array $tarif): array
    {
        $seri = (int) ($row['seriBarang'] ?? 0);
        $bahanBaku = array_values(array_filter($semuaBahanBaku, fn ($bb) => (int) ($bb['seriBarang'] ?? 0) === $seri));
        $netto = $row['netto'] ?? null;
        $cifRupiah = $row['cifRupiah'] ?? null;
        $satuan = (string) ($row['kodeSatuanBarang'] ?? '');

        return [
            'idBarang' => '0',
            'cif' => (float) ($row['cif'] ?? 0),
            'cifRupiah' => round((float) ($cifRupiah ?? 0), 2),
            'hargaEkspor' => $this->num($row['hargaEkspor'] ?? null, 0),
            'hargaPenyerahan' => $this->num($row['hargaPenyerahan'] ?? null, 0),
            'isiPerKemasan' => $this->num($row['isiPerKemasan'] ?? null, 0),
            'jumlahKemasan' => (float) ($row['jumlahKemasan'] ?? 0),
            'jumlahSatuan' => (float) ($row['jumlahSatuan'] ?? 0),
            'kodeAsalBahanBaku' => '0',
            'kodeAsalBarang' => '0',
            'kodeBarang' => (string) ($row['kodeBarang'] ?? ''),
            'kodeDokumen' => '262',
            'kodeJenisKemasan' => $this->strOrNull($row['kodeJenisKemasan'] ?? null),
            'kodeNegaraAsal' => (string) ($row['kodeNegaraAsal'] ?? ''),
            'kodeSatuanBarang' => $satuan,
            'merk' => (string) self::orDefault($row['merk'] ?? null, '-'),
            'ndpbm' => $this->num($row['ndpbm'] ?? null, 0),
            'netto' => round((float) ($netto ?? 0), 2),
            'nilaiBarang' => $this->num($row['nilaiBarang'] ?? null, 0),
            'nilaiJasa' => round((float) ($cifRupiah ?? 0), 2),
            'posTarif' => (string) ($row['posTarif'] ?? ''),
            'seriBarang' => $seri,
            'spesifikasiLain' => (string) self::orDefault($row['spesifikasiLain'] ?? null, '0'),
            'hargaPerolehan' => $this->num($row['hargaPerolehan'] ?? null, 0),
            'tipe' => (string) self::orDefault($row['tipe'] ?? null, '0'),
            'uangMuka' => 0,
            'ukuran' => (string) self::orDefault($row['ukuran'] ?? null, '0'),
            'uraian' => (string) ($row['uraian'] ?? ''),
            'diskon' => $this->num($row['diskon'] ?? null, 0),
            'freight' => $this->num($row['freight'] ?? null, 0),
            'hargaSatuan' => $this->num($row['hargaSatuan'] ?? null, 0),
            'kodeKategoriBarang' => $this->strOrNull($row['kodeKategoriBarang'] ?? null),
            'kodePerhitungan' => (string) self::orDefault($row['kodePerhitungan'] ?? null, '0'),
            'bahanBaku' => array_map(fn ($bb) => $this->bahanBaku($bb), $bahanBaku),
            'barangTarif' => $this->barangTarif($row, $satuan, $tarif),
            'nilaiDiskon' => 0,
            'volume' => null,
            'persenBM' => (float) ($row['persenBM'] ?? 0),
            'persenPPN' => (float) ($row['persenPPN'] ?? 0),
            'persenPPh' => (float) ($row['persenPPh'] ?? 0),
            'barangDokumen' => [],
        ];
    }

    // §9.10 - tarif BM 40 / PPh 2.5 / PPN 11 bawaan Villiger. Bila tab Pungutan terisi (Q-c): tarif
    // memakai persen barang bila > 0 (selain itu tarif bawaan) dan nilaiBayar = bagian dari nilai
    // Pungutan (lihat tarifBarang()). Urutan key BM berbeda dengan PPH/PPN (kodeJenisPungutan
    // sebelum kodeSatuanBarang). jumlahSatuan ketiganya dibulatkan 2 desimal.
    private function barangTarif(array $row, string $satuan, array $tarif): array
    {
        $netto = $this->round2($row['netto'] ?? null, 0);
        $persen = function (string $key, float $bawaan) use ($row, $tarif) {
            $p = (float) ($row[$key] ?? 0);

            return $tarif['dariPungutan'] && $p > 0 ? $p : $bawaan;
        };

        return [
            [
                'kodeJenisTarif' => '1',
                'jumlahSatuan' => $netto,
                'kodeFasilitasTarif' => '3',
                'kodeJenisPungutan' => 'BM',
                'kodeSatuanBarang' => $satuan,
                'nilaiBayar' => $tarif['BM'],
                'nilaiFasilitas' => 0,
                'nilaiSudahDilunasi' => 0,
                'seriBarang' => 1,
                'tarif' => $persen('persenBM', 40),
                'tarifFasilitas' => 100,
            ],
            [
                'kodeJenisTarif' => '1',
                'jumlahSatuan' => $netto,
                'kodeFasilitasTarif' => '6',
                'kodeSatuanBarang' => $satuan,
                'kodeJenisPungutan' => 'PPH',
                'nilaiBayar' => $tarif['PPH'],
                'nilaiFasilitas' => 0,
                'nilaiSudahDilunasi' => 0,
                'seriBarang' => 2,
                'tarif' => $persen('persenPPh', 2.5),
                'tarifFasilitas' => 100,
            ],
            [
                'kodeJenisTarif' => '1',
                'jumlahSatuan' => $netto,
                'kodeFasilitasTarif' => '6',
                'kodeSatuanBarang' => $satuan,
                'kodeJenisPungutan' => 'PPN',
                'nilaiBayar' => $tarif['PPN'],
                'nilaiFasilitas' => 0,
                'nilaiSudahDilunasi' => 0,
                'seriBarang' => 3,
                'tarif' => $persen('persenPPN', 11),
                'tarifFasilitas' => 100,
            ],
        ];
    }

    // §9.9 - key set dan urutan berbeda dari BC 2.6.1 (tanpa flagTis / isiPerKemasan / nomorDokumen).
    private function bahanBaku(array $bb): array
    {
        $asal = (string) self::orDefault($bb['kodeAsalBahanBaku'] ?? null, '0');
        $satuan = (string) ($bb['kodeSatuanBarang'] ?? '');
        $seriBahanBaku = (int) ($bb['seriBahanBaku'] ?? 0);

        return [
            'cif' => $this->num($bb['cif'] ?? null, 0),
            'cifRupiah' => $this->round2($bb['cifRupiah'] ?? null, 0),
            'hargaPenyerahan' => $this->num($bb['hargaPenyerahan'] ?? null, 0),
            'hargaPerolehan' => $this->num($bb['hargaPerolehan'] ?? null, 0),
            'jumlahSatuan' => $this->num($bb['jumlahSatuan'] ?? null, 0),
            'kodeSatuanBarang' => $satuan,
            'kodeAsalBahanBaku' => $asal,
            'kodeBarang' => (string) ($bb['kodeBarang'] ?? ''),
            'kodeDokAsal' => (string) ($bb['kodeDokumen'] ?? ''),
            'kodeDokumen' => '262',
            'kodeKantor' => (string) ($bb['kodeKantorAsal'] ?? ''),
            'merkBarang' => (string) self::orDefault($bb['merkBarang'] ?? null, ''),
            'ndpbm' => 0,
            'netto' => $this->round2($bb['netto'] ?? null, 0),
            'nomorAjuDokAsal' => $this->strOrNull($bb['nomorAjuDokAsal'] ?? null),
            'nomorDaftarDokAsal' => (string) ($bb['nomorDaftarDokAsal'] ?? ''),
            'posTarif' => (string) ($bb['posTarif'] ?? ''),
            'seriBahanBaku' => $seriBahanBaku,
            'seriBarang' => (int) ($bb['seriBarang'] ?? 0),
            'seriBarangDokAsal' => (int) self::orDefault($bb['seriBarangDokAsal'] ?? null, 1),
            'seriIjin' => 1,
            'spesifikasiLainBarang' => (string) self::orDefault($bb['spesifikasiLainBarang'] ?? null, ''),
            'tanggalDaftarDokAsal' => $this->date($bb['tanggalDaftarDokAsal'] ?? null) ?? '',
            'tipeBarang' => (string) self::orDefault($bb['tipeBarang'] ?? null, ''),
            'ukuranBarang' => (string) self::orDefault($bb['ukuranBarang'] ?? null, ''),
            'uraianBarang' => (string) ($bb['uraianBarang'] ?? ''),
            'nilaiJasa' => $this->num($bb['nilaiJasa'] ?? null, 0),
            'bahanBakuTarif' => [[
                'jumlahSatuan' => $this->num($bb['jumlahSatuan'] ?? null, 0),
                'kodeFasilitasTarif' => '3',
                'kodeAsalBahanBaku' => $asal,
                'kodeJenisPungutan' => 'PPN',
                'kodeSatuanBarang' => $satuan,
                'kodeJenisTarif' => '1',
                'nilaiBayar' => $this->round2($bb['cifRupiah'] ?? null, 0),
                'nilaiSudahDilunasi' => 0,
                'nilaiFasilitas' => 0,
                'seriBahanBaku' => $seriBahanBaku ?: 1,
                'tarif' => 11,
                'tarifFasilitas' => 100,
                'jumlahKemasan' => 0,
            ]],
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
