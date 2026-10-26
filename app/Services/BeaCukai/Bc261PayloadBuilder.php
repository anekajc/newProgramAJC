<?php

namespace App\Services\BeaCukai;

// Penyusun JSON CEISA untuk BC 2.6.1 - salinan persis pengirim Villiger (docs/documentbc261_plan.md §9).
// CEISA menolak payload yang urutan key-nya berbeda, jadi setiap objek disusun manual dengan urutan
// key yang sama dengan tabel di §9. Jangan pakai array_merge/toArray untuk menyusunnya.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase (nama logis di §2) yang nilainya
// sudah bertipe PHP - desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null.
// Konversi dari baris SQL Server dikerjakan controller.
class Bc261PayloadBuilder
{
    /**
     * @param  array  $header  header dokumen (§2.1)
     * @param  array  $children  ['barang' => [...], 'bahanbaku' => [...], 'kemasan' => [...], 'jaminan' => [...], 'dokumen' => [...]]
     * @param  array  $entitas  [entitas untuk selectEntitas1, ...2, ...3] dari master ENTITAS; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));
        $bahanBaku = array_values($children['bahanbaku'] ?? []);

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
            'kodeDokumen' => '261',
            'kodeKantor' => (string) ($header['kodeKantor'] ?? ''),
            'kodeTujuanPengiriman' => (string) ($header['kodeTujuanPengiriman'] ?? ''),
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
            'barang' => array_map(fn ($row) => $this->barang($row, $bahanBaku), $barang),
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'entitas' => $this->entitas($entitas),
            'jaminan' => array_map(fn ($row) => $this->jaminan($row), array_values($children['jaminan'] ?? [])),
            'kemasan' => array_map(fn ($row) => $this->kemasan($row), array_values($children['kemasan'] ?? [])),
            'pengangkut' => [[
                'idPengangkut' => '1',
                'kodeBendera' => '',
                'namaPengangkut' => '',
                'nomorPengangkut' => '',
                'kodeCaraAngkut' => (string) self::orDefault($header['kodeCaraAngkut'] ?? null, ''),
                'seriPengangkut' => 1,
            ]],
            // Q-c: satu baris PPN senilai CIF Rupiah barang PERTAMA saja.
            'pungutan' => [[
                'idPungutan' => '1',
                'kodeFasilitasTarif' => '3',
                'kodeJenisPungutan' => 'PPN',
                'nilaiPungutan' => $barang ? round((float) ($barang[0]['cifRupiah'] ?? 0), 2) : 0,
            ]],
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

    // §9.2: entitas diisi "berikutnya yang tidak kosong" ke slot seri 3 → 7 → 8 (Q-d).
    private function entitas(array $entitas): array
    {
        $isi = array_values(array_filter(array_slice($entitas, 0, 3), fn ($e) => ! empty($e)));
        $e = fn (int $i, string $key, $default = '') => isset($isi[$i]) ? self::orDefault($isi[$i][$key] ?? null, $default) : $default;

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
                'tanggalIjinEntitas' => isset($isi[0]) ? ($this->date($isi[0]['tanggalIjinEntitas'] ?? null) ?? '') : '',
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
                'kodeEntitas' => '8',
                'kodeJenisApi' => (string) $e(2, 'kodeJenisApi', '02'),
                'kodeJenisIdentitas' => (string) $e(2, 'kodeJenisIdentitas', '6'),
                'kodeStatus' => (string) $e(2, 'kodeStatus', '0'),
                'namaEntitas' => (string) $e(2, 'namaEntitas'),
                'nomorIdentitas' => (string) $e(2, 'nomorIdentitas'),
                'nomorIjinEntitas' => (string) $e(2, 'nomorIjinEntitas'),
                'tanggalIjinEntitas' => isset($isi[2]) ? ($this->date($isi[2]['tanggalIjinEntitas'] ?? null) ?? '') : '',
                'seriEntitas' => 8,
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

    // §9.5 - seriKemasan dikirim sebagai string apa adanya; fallback angka 1.
    private function kemasan(array $row): array
    {
        $seri = self::orDefault($row['seriKemasan'] ?? null, 1);

        return [
            'jumlahKemasan' => (int) self::orDefault($row['jumlahKemasan'] ?? null, 0),
            'kodeJenisKemasan' => (string) self::orDefault($row['kodeJenisKemasan'] ?? null, ''),
            'seriKemasan' => is_int($seri) ? $seri : (string) $seri,
            'merkKemasan' => (string) self::orDefault($row['merkKemasan'] ?? null, ''),
        ];
    }

    // §9.8
    private function barang(array $row, array $semuaBahanBaku): array
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
            'kodeDokumen' => '261',
            'kodeJenisKemasan' => $this->strOrNull($row['kodeJenisKemasan'] ?? null),
            'kodeNegaraAsal' => (string) ($row['kodeNegaraAsal'] ?? ''),
            'kodeSatuanBarang' => $satuan,
            'merk' => (string) self::orDefault($row['merk'] ?? null, '-'),
            'ndpbm' => $this->num($row['ndpbm'] ?? null, 0),
            'netto' => round((float) ($netto ?? 0), 2),
            'nilaiBarang' => $this->num($cifRupiah, 0),
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
            // Q-b: tarif BM 40 / PPh 2.5 / PPN 11 ditanam; nilaiBayar = CIF Rupiah penuh.
            // Urutan key BM berbeda dengan PPH/PPN (kodeJenisPungutan sebelum kodeSatuanBarang).
            'barangTarif' => [
                [
                    'kodeJenisTarif' => '1',
                    'jumlahSatuan' => $this->round2($netto, 0),
                    'kodeFasilitasTarif' => '3',
                    'kodeJenisPungutan' => 'BM',
                    'kodeSatuanBarang' => $satuan,
                    'nilaiBayar' => $this->round2($cifRupiah, 0),
                    'nilaiFasilitas' => 0,
                    'nilaiSudahDilunasi' => 0,
                    'seriBarang' => 1,
                    'tarif' => 40,
                    'tarifFasilitas' => 100,
                ],
                [
                    'kodeJenisTarif' => '1',
                    'jumlahSatuan' => $this->num($netto, 0),
                    'kodeFasilitasTarif' => '6',
                    'kodeSatuanBarang' => $satuan,
                    'kodeJenisPungutan' => 'PPH',
                    'nilaiBayar' => $this->round2($cifRupiah, 0),
                    'nilaiFasilitas' => 0,
                    'nilaiSudahDilunasi' => 0,
                    'seriBarang' => 2,
                    'tarif' => 2.5,
                    'tarifFasilitas' => 100,
                ],
                [
                    'kodeJenisTarif' => '1',
                    'jumlahSatuan' => $this->round2($netto, 0),
                    'kodeFasilitasTarif' => '6',
                    'kodeSatuanBarang' => $satuan,
                    'kodeJenisPungutan' => 'PPN',
                    'nilaiBayar' => $this->round2($cifRupiah, 0),
                    'nilaiFasilitas' => 0,
                    'nilaiSudahDilunasi' => 0,
                    'seriBarang' => 3,
                    'tarif' => 11,
                    'tarifFasilitas' => 100,
                ],
            ],
            'nilaiDiskon' => 0,
            'volume' => null,
            'persenBM' => (float) ($row['persenBM'] ?? 0),
            'persenPPN' => (float) ($row['persenPPN'] ?? 0),
            'persenPPh' => (float) ($row['persenPPh'] ?? 0),
            'barangDokumen' => [],
        ];
    }

    // §9.9 - seriBarangDokAsal & bahanBakuTarif.seriBahanBaku ditanam 1 (Q-i).
    private function bahanBaku(array $bb): array
    {
        $asal = (string) self::orDefault($bb['kodeAsalBahanBaku'] ?? null, '0');
        $satuan = (string) ($bb['kodeSatuanBarang'] ?? '');

        return [
            'seriBarangDokAsal' => 1,
            'cif' => $this->num($bb['cif'] ?? null, 0),
            'cifRupiah' => $this->round2($bb['cifRupiah'] ?? null, 0),
            'flagTis' => '',
            'hargaPenyerahan' => $this->num($bb['hargaPenyerahan'] ?? null, 0),
            'hargaPerolehan' => $this->num($bb['hargaPerolehan'] ?? null, 0),
            'isiPerKemasan' => 0,
            'jumlahSatuan' => $this->num($bb['jumlahSatuan'] ?? null, 0),
            'kodeAsalBahanBaku' => $asal,
            'kodeBarang' => (string) ($bb['kodeBarang'] ?? ''),
            'kodeDokAsal' => (string) ($bb['kodeDokumen'] ?? ''),
            'kodeDokumen' => '261',
            'kodeKantor' => (string) ($bb['kodeKantorAsal'] ?? ''),
            'kodeSatuanBarang' => $satuan,
            'merkBarang' => (string) self::orDefault($bb['merkBarang'] ?? null, ''),
            'ndpbm' => $this->num($bb['ndpbm'] ?? null, 0),
            'netto' => $this->num($bb['netto'] ?? null, 0),
            'nilaiJasa' => $this->num($bb['nilaiJasa'] ?? null, 0),
            'nomorDaftarDokAsal' => (string) ($bb['nomorDaftarDokAsal'] ?? ''),
            'nomorDokumen' => (string) ($bb['nomorDaftarDokAsal'] ?? ''),
            'posTarif' => (string) ($bb['posTarif'] ?? ''),
            'seriBahanBaku' => (int) ($bb['seriBahanBaku'] ?? 0),
            'seriBarang' => (int) ($bb['seriBarang'] ?? 0),
            'seriIjin' => (int) self::orDefault($bb['seriIjin'] ?? null, 1),
            'spesifikasiLainBarang' => (string) self::orDefault($bb['spesifikasiLainBarang'] ?? null, ''),
            'tanggalDaftarDokAsal' => $this->date($bb['tanggalDaftarDokAsal'] ?? null) ?? '',
            'tipeBarang' => (string) self::orDefault($bb['tipeBarang'] ?? null, ''),
            'ukuranBarang' => (string) self::orDefault($bb['ukuranBarang'] ?? null, ''),
            'uraianBarang' => (string) ($bb['uraianBarang'] ?? ''),
            'bahanBakuTarif' => [[
                'jumlahKemasan' => 0,
                'jumlahSatuan' => $this->num($bb['jumlahSatuan'] ?? null, 0),
                'kodeAsalBahanBaku' => $asal,
                'kodeFasilitasTarif' => '3',
                'kodeJenisPungutan' => 'PPN',
                'kodeJenisTarif' => '1',
                'kodeSatuanBarang' => $satuan,
                'nilaiBayar' => $this->round2($bb['cifRupiah'] ?? null, 0),
                'nilaiFasilitas' => 0,
                'nilaiSudahDilunasi' => 0,
                'seriBahanBaku' => 1,
                'tarif' => 11,
                'tarifFasilitas' => 100,
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
