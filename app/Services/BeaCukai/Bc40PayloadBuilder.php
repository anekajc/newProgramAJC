<?php

namespace App\Services\BeaCukai;

// Penyusun JSON CEISA untuk BC 4.0 - salinan persis pengirim Villiger (docs/documentbc40_plan.md §9).
// CEISA menolak payload yang urutan key-nya berbeda, jadi setiap objek disusun manual dengan urutan
// key yang sama dengan tabel di §9. Jangan pakai array_merge/toArray untuk menyusunnya. Perhatikan
// urutan akar yang diselang-seling (array entitas ... barang, lalu idPengguna ... nilaiJasa, lalu
// dokumen dan kontainer) dan ejaan khusus BC 4.0: isiPerkemasan, kodeTujuanPengiriman.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase (nama logis di §2) yang nilainya
// sudah bertipe PHP - desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null.
// Konversi dari baris SQL Server dikerjakan controller. idPengguna dikirim lewat $header['idPengguna']
// (username ERP yang login, Q-c).
class Bc40PayloadBuilder
{
    /**
     * @param  array  $header  header dokumen (§2.1), termasuk kemasan / pengangkut tunggal
     * @param  array  $children  ['barang' => [...], 'dokumen' => [...]]
     * @param  array  $entitas  [entitas untuk selectEntitas1, ...2, ...3] dari master ENTITAS; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));

        return [
            'asalData' => 'S',
            'asuransi' => 0.0,
            'bruto' => $this->flt($header['bruto'] ?? null),
            'kodeJenisTpb' => $this->strOrNull($header['kodeJenisTpb'] ?? null),
            'hargaPenyerahan' => $this->flt($header['hargaPenyerahan'] ?? null),
            'jabatanTtd' => $this->strOrNull($header['jabatanTtd'] ?? null),
            'kodeDokumen' => '40',
            'kodeKantor' => $this->strOrNull($header['kodeKantor'] ?? null),
            // Dikirim sebagai kodeTujuanPengiriman, disimpan sebagai kodeTujuanPengirim.
            'kodeTujuanPengiriman' => (string) self::orDefault($header['kodeTujuanPengirim'] ?? null, ''),
            'kotaTtd' => $this->strOrNull($header['kotaTtd'] ?? null),
            'namaTtd' => $this->strOrNull($header['namaTtd'] ?? null),
            'netto' => $this->flt($header['netto'] ?? null),
            'nomorAju' => $this->strOrNull($header['nomorAju'] ?? null),
            'seri' => 1,
            'tanggalAju' => $this->date($header['tanggalAju'] ?? null),
            'tanggalTtd' => $this->date($header['tanggalTtd'] ?? null),
            'entitas' => $this->entitas($entitas),
            'pengangkut' => [[
                'namaPengangkut' => (string) self::orDefault($header['namaPengangkut'] ?? null, ''),
                'nomorPengangkut' => (string) self::orDefault($header['nomorPengangkut'] ?? null, ''),
                'seriPengangkut' => 1,
            ]],
            'kemasan' => $this->kemasan($header),
            // Q-b: satu baris PPN dengan nilaiPungutan 0.
            'pungutan' => [[
                'kodeFasilitasTarif' => '3',
                'kodeJenisPungutan' => 'PPN',
                'nilaiPungutan' => 0,
            ]],
            'barang' => array_map(fn ($row) => $this->barang($row, $header), $barang),
            'idPengguna' => (string) ($header['idPengguna'] ?? ''),
            'jumlahKontainer' => 0,
            'nik' => '',
            'volume' => $this->flt($header['volume'] ?? null),
            'biayaTambahan' => 0.0,
            'biayaPengurang' => 0.0,
            'vd' => 0.0,
            'uangMuka' => $this->flt($header['uangMuka'] ?? null),
            'nilaiJasa' => $this->flt($header['nilaiJasa'] ?? null),
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'kontainer' => [],
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
        return Bc261PayloadBuilder::orDefault($value, $default);
    }

    // §9.2: entitas diisi "berikutnya yang tidak kosong" ke slot seri 3 → 7 → 9 (Q-d). Di slot seri 3
    // seriEntitas SEBELUM tanggalIjinEntitas; slot 7 dan 9 punya key yang sama.
    private function entitas(array $entitas): array
    {
        $isi = array_values(array_filter(array_slice($entitas, 0, 3), fn ($e) => ! empty($e)));
        $e = fn (int $i, string $key, $default = '') => isset($isi[$i]) ? self::orDefault($isi[$i][$key] ?? null, $default) : $default;
        $pihak = fn (int $i, string $kode, int $seri) => [
            'alamatEntitas' => (string) $e($i, 'alamatEntitas'),
            'kodeEntitas' => $kode,
            'kodeJenisApi' => (string) $e($i, 'kodeJenisApi', '02'),
            'kodeJenisIdentitas' => (string) $e($i, 'kodeJenisIdentitas', '6'),
            'kodeStatus' => (string) $e($i, 'kodeStatus', '0'),
            'namaEntitas' => (string) $e($i, 'namaEntitas'),
            'nibEntitas' => (string) $e($i, 'nibEntitas'),
            'nomorIdentitas' => (string) $e($i, 'nomorIdentitas'),
            'seriEntitas' => $seri,
        ];

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
                'tanggalIjinEntitas' => isset($isi[0]) ? ($this->date($isi[0]['tanggalIjinEntitas'] ?? null) ?? '') : '',
            ],
            $pihak(1, '7', 7),
            $pihak(2, '9', 9),
        ];
    }

    // §9.3 - tidak ada idDokumen.
    private function dokumen(array $row): array
    {
        return [
            'kodeDokumen' => (string) ($row['kodeDokumen'] ?? ''),
            'nomorDokumen' => $this->strOrNull($row['nomorDokumen'] ?? null),
            'seriDokumen' => (int) ($row['seriDokumen'] ?? 0),
            'tanggalDokumen' => $this->date($row['tanggalDokumen'] ?? null),
        ];
    }

    // §9.5: hanya bila jenis kemasan ATAU jumlah kemasan header terisi. Urutan: jumlah, jenis, MERK, seri
    // (BC 2.3: jumlah, jenis, seri, merk). Nilai apa adanya (raw).
    private function kemasan(array $header): array
    {
        $jenis = self::orDefault($header['kodeJenisKemasan'] ?? null, '');
        $jumlah = self::orDefault($header['jumlahKemasan'] ?? null, 0);
        if ($jenis === '' && $jumlah === 0) {
            return [];
        }

        return [[
            'jumlahKemasan' => $jumlah === 0 ? null : (int) $jumlah,
            'kodeJenisKemasan' => $this->strOrNull($header['kodeJenisKemasan'] ?? null),
            'merkKemasan' => $this->strOrNull($header['merkKemasan'] ?? null),
            'seriKemasan' => 1,
        ]];
    }

    // §9.4 - 32 key, tanpa idBarang / barangDokumen / persen* / nilaiDiskon / kodeNegaraAsal.
    private function barang(array $row, array $header): array
    {
        $netto = $row['netto'] ?? null;
        $seri = (int) ($row['seriBarang'] ?? 0);
        $harga = (float) ($row['hargaPenyerahan'] ?? 0);
        $satuan = $this->strOrNull($row['kodeSatuanBarang'] ?? null);

        return [
            'asuransi' => 0.0,
            // Q-g: bruto dan uangMuka / nilaiJasa header disalin ke setiap barang.
            'bruto' => $this->flt($header['bruto'] ?? null),
            'cif' => 0.0,
            'diskon' => 0.0,
            'hargaEkspor' => 0.0,
            'hargaPenyerahan' => $this->flt($harga),
            'hargaSatuan' => 0.0,
            'isiPerkemasan' => null,
            'jumlahKemasan' => ($row['jumlahKemasan'] ?? null) === null ? null : (int) $row['jumlahKemasan'],
            'jumlahRealisasi' => $harga, // Q-j: nilai uang di kolom jumlah
            'jumlahSatuan' => $this->fltOrNull($row['jumlahSatuan'] ?? null),
            'kodeBarang' => $this->strOrNull($row['kodeBarang'] ?? null),
            'kodeDokumen' => '40',
            'kodeJenisKemasan' => $this->strOrNull($row['kodeJenisKemasan'] ?? null),
            'kodeSatuanBarang' => $satuan,
            'merk' => $this->strOrNull($row['merk'] ?? null),
            'netto' => $this->fltOrNull($netto),
            'nilaiBarang' => 0,
            'posTarif' => $this->strOrNull($row['posTarif'] ?? null),
            'seriBarang' => $seri,
            'spesifikasiLain' => $this->strOrNull($row['spesifikasiLain'] ?? null),
            'tipe' => $this->strOrNull($row['tipe'] ?? null),
            'ukuran' => $this->strOrNull($row['ukuran'] ?? null),
            'uraian' => $this->strOrNull($row['uraian'] ?? null),
            'volume' => $this->fltOrNull($row['volume'] ?? null),
            'cifRupiah' => 0.0, // Q-b: dari cif barang yang selalu 0
            'hargaPerolehan' => 0.0,
            'kodeAsalBahanBaku' => '1',
            'ndpbm' => 0.0,
            'uangMuka' => $this->flt($header['uangMuka'] ?? null),
            'nilaiJasa' => $this->flt($header['nilaiJasa'] ?? null),
            // Q-h: tarif PPN 11 ditanam; kodeJenisPungutan key TERAKHIR; seriBarang = seri barangnya;
            // nilaiBayar dari cif barang (selalu 0).
            'barangTarif' => [[
                'kodeJenisTarif' => '1',
                'jumlahSatuan' => $this->num($netto, 0),
                'kodeFasilitasTarif' => '3',
                'kodeSatuanBarang' => $satuan,
                'nilaiBayar' => 0,
                'nilaiFasilitas' => 0,
                'nilaiSudahDilunasi' => 0,
                'seriBarang' => $seri,
                'tarif' => 11,
                'tarifFasilitas' => 100,
                'kodeJenisPungutan' => 'PPN',
            ]],
        ];
    }

    // Selalu float (0.0 bila kosong).
    private function flt($value): float
    {
        return (float) self::orDefault($value, 0);
    }

    // Tanpa fallback: null tetap null, selain itu float apa adanya.
    private function fltOrNull($value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    // Angka: fallback bila kosong/0, selain itu float apa adanya.
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
