<?php

namespace App\Services\BeaCukai;

use App\Services\BeaCukai\Concerns\ValutaIso;

// Penyusun JSON CEISA untuk BC 2.3 - salinan persis pengirim Villiger (docs/documentbc23_plan.md §9).
// CEISA menolak payload yang urutan key-nya berbeda, jadi setiap objek disusun manual dengan urutan
// key yang sama dengan tabel di §9. Jangan pakai array_merge/toArray untuk menyusunnya.
//
// Kelas ini murni (tanpa DB): masukannya array ber-key camelCase (nama logis di §2) yang nilainya
// sudah bertipe PHP - desimal float|null, bilangan bulat int|null, tanggal 'Y-m-d'|null, teks string|null.
// Konversi dari baris SQL Server dikerjakan controller.
//
// Beda dengan Villiger (keputusan user, plan §11 + pengecekan skema resmi docs/bc_schema/bc23.json):
//   - tanggalTiba dikirim sesuai isian dokumen (Q-a, tanpa tanggal tetap "2026-06-02");
//   - jumlahKontainer = banyaknya peti kemas yang dikirim (Q-h);
//   - kodeValuta diterjemahkan ke kode ISO lewat config beacukai.valuta_iso (RM -> MYR, EURO -> EUR);
//   - cif / fob / freight / asuransi / nilaiBarang dibulatkan 2 desimal (skema: kelipatan 0.01);
//   - entitas slot tetap E1->seri 3, E2->seri 5, E3->seri 7 (ketiganya wajib; tidak digeser lagi) dan Pemilik
//     Barang tidak mengirim nomor/tanggal izin yang kosong (opsional di skema, "" bukan tanggal valid);
//   - key yang tidak ada di skema barang (nilaiDiskon, volume, nilaiJasa, persenBM/PPN/PPh) tidak dikirim.
// Selebihnya payload sama persis, termasuk nilai tanam (barangTarif BM 40 / PPh 2.5 / PPN 11).
class Bc23PayloadBuilder
{
    use ValutaIso;

    /**
     * @param  array  $header  header dokumen (§2.1), termasuk kemasan / kontainer / pengangkut tunggal
     * @param  array  $children  ['barang' => [...], 'dokumen' => [...]]
     * @param  array  $entitas  [entitas untuk selectEntitas1, ...2, ...3] dari master ENTITAS; null = kosong
     */
    public function build(array $header, array $children, array $entitas): array
    {
        $barang = array_values($children['barang'] ?? []);
        usort($barang, fn ($a, $b) => ((int) ($a['seriBarang'] ?? 0)) <=> ((int) ($b['seriBarang'] ?? 0)));
        $kontainer = $this->kontainer($header);

        return [
            'asalData' => 'S',
            'asuransi' => $this->flt2($header['asuransi'] ?? null),
            'bruto' => $this->flt($header['bruto'] ?? null),
            'cif' => $this->flt2($header['cif'] ?? null),
            'fob' => $this->flt2($header['fob'] ?? null),
            'freight' => $this->flt2($header['freight'] ?? null),
            'hargaPenyerahan' => $this->flt($header['hargaPenyerahan'] ?? null),
            'jabatanTtd' => $this->strOrNull($header['jabatanTtd'] ?? null),
            'jumlahKontainer' => count($kontainer),
            'kodeAsuransi' => (string) self::orDefault($header['kodeAsuransi'] ?? null, ''),
            'kodeDokumen' => '23',
            'kodeIncoterm' => (string) self::orDefault($header['kodeIncoterm'] ?? null, ''),
            'kodeKantor' => $this->strOrNull($header['kodeKantor'] ?? null),
            'kodeKantorBongkar' => (string) self::orDefault($header['kodeKantorBongkar'] ?? null, ''),
            'kodePelBongkar' => (string) self::orDefault($header['kodePelBongkar'] ?? null, ''),
            'kodePelMuat' => (string) self::orDefault($header['kodePelMuat'] ?? null, ''),
            'kodePelTransit' => (string) self::orDefault($header['kodePelTransit'] ?? null, ''),
            'kodeTps' => (string) self::orDefault($header['kodeTps'] ?? null, ''),
            'kodeTujuanTpb' => (string) self::orDefault($header['kodeTujuanTpb'] ?? null, ''),
            'kodeTutupPu' => '11',
            'kodeValuta' => $this->valutaIso((string) self::orDefault($header['kodeValuta'] ?? null, '')),
            'kotaTtd' => $this->strOrNull($header['kotaTtd'] ?? null),
            'namaTtd' => $this->strOrNull($header['namaTtd'] ?? null),
            'ndpbm' => $this->flt($header['ndpbm'] ?? null),
            'netto' => $this->flt($header['netto'] ?? null), // tidak dibulatkan
            'nik' => '0',
            'nilaiBarang' => $this->flt2($header['cif'] ?? null),
            'nomorAju' => $this->strOrNull($header['nomorAju'] ?? null),
            'nomorBc11' => $this->strOrNull($header['nomorBc11'] ?? null),
            'posBc11' => $this->strOrNull($header['posBc11'] ?? null),
            'seri' => 1,
            'subposBc11' => $this->strOrNull($header['subPosBc11'] ?? null), // "p" huruf kecil
            'tanggalBc11' => $this->date($header['tanggalBc11'] ?? null) ?? '',
            'tanggalTiba' => $this->date($header['tanggalTiba'] ?? null) ?? '',
            'tanggalTtd' => $this->date($header['tanggalTtd'] ?? null) ?? '',
            'biayaTambahan' => 0.0,
            'biayaPengurang' => 0.0,
            'kodeKenaPajak' => '1',
            'barang' => array_map(fn ($row) => $this->barang($row), $barang),
            'entitas' => $this->entitas($entitas),
            'kemasan' => $this->kemasan($header),
            'kontainer' => $kontainer,
            'dokumen' => array_map(fn ($row) => $this->dokumen($row), array_values($children['dokumen'] ?? [])),
            'pengangkut' => [[
                'kodeBendera' => (string) self::orDefault($header['kodeBendera'] ?? null, ''),
                'namaPengangkut' => (string) self::orDefault($header['namaPengangkut'] ?? null, ''),
                'nomorPengangkut' => (string) self::orDefault($header['nomorPengangkut'] ?? null, ''),
                'kodeCaraAngkut' => (string) self::orDefault($header['kodeCaraAngkut'] ?? null, ''),
                'seriPengangkut' => 0,
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
        return Bc261PayloadBuilder::orDefault($value, $default);
    }

    // §9.2: slot tetap seri 3 (Pengusaha TPB) -> 5 (Pemasok) -> 7 (Pemilik Barang); ketiganya wajib diisi
    // (dicek saat kirim), jadi slot kosong tidak lagi menggeser entitas lain. Pemilik Barang hanya mengirim
    // nomor / tanggal izin bila terisi (opsional di skema; "" bukan tanggal yang valid).
    private function entitas(array $entitas): array
    {
        $e = fn (int $i, string $key, $default = '') => ! empty($entitas[$i]) ? self::orDefault($entitas[$i][$key] ?? null, $default) : $default;
        $tgl = fn (int $i) => ! empty($entitas[$i]) ? ($this->date($entitas[$i]['tanggalIjinEntitas'] ?? null) ?? '') : '';

        $pemilik = [
            'alamatEntitas' => (string) $e(2, 'alamatEntitas'),
            'kodeEntitas' => '7',
            'kodeJenisApi' => (string) $e(2, 'kodeJenisApi', '02'),
            'kodeJenisIdentitas' => (string) $e(2, 'kodeJenisIdentitas', '6'),
            'kodeStatus' => (string) $e(2, 'kodeStatus', '0'),
            'namaEntitas' => (string) $e(2, 'namaEntitas'),
            'nomorIdentitas' => (string) $e(2, 'nomorIdentitas'),
        ];
        if ((string) $e(2, 'nomorIjinEntitas') !== '') {
            $pemilik['nomorIjinEntitas'] = (string) $e(2, 'nomorIjinEntitas');
        }
        if ($tgl(2) !== '') {
            $pemilik['tanggalIjinEntitas'] = $tgl(2);
        }
        $pemilik['seriEntitas'] = 7;

        return [
            [
                'alamatEntitas' => (string) $e(0, 'alamatEntitas'),
                'kodeEntitas' => '3',
                'kodeJenisIdentitas' => (string) $e(0, 'kodeJenisIdentitas', '6'),
                'namaEntitas' => (string) $e(0, 'namaEntitas'),
                'nibEntitas' => (string) $e(0, 'nibEntitas'),
                'nomorIdentitas' => (string) $e(0, 'nomorIdentitas'),
                'nomorIjinEntitas' => (string) $e(0, 'nomorIjinEntitas'),
                'tanggalIjinEntitas' => $tgl(0),
                'seriEntitas' => 3,
            ],
            [
                'alamatEntitas' => (string) $e(1, 'alamatEntitas'),
                'kodeEntitas' => '5',
                'kodeNegara' => (string) $e(1, 'kodeNegara'),
                'namaEntitas' => (string) $e(1, 'namaEntitas'),
                'seriEntitas' => 5,
            ],
            $pemilik,
        ];
    }

    // §9.3
    private function dokumen(array $row): array
    {
        return [
            'idDokumen' => '0',
            'kodeDokumen' => $this->strOrNull($row['kodeDokumen'] ?? null),
            'nomorDokumen' => $this->strOrNull($row['nomorDokumen'] ?? null),
            'seriDokumen' => (int) ($row['seriDokumen'] ?? 0),
            'tanggalDokumen' => $this->date($row['tanggalDokumen'] ?? null),
        ];
    }

    // §9.5: hanya bila jenis kemasan ATAU jumlah kemasan header terisi; seriKemasan selalu 1.
    private function kemasan(array $header): array
    {
        $jenis = self::orDefault($header['kodeJenisKemasan'] ?? null, '');
        $jumlah = self::orDefault($header['jumlahKemasan'] ?? null, 0);
        if ($jenis === '' && $jumlah === 0) {
            return [];
        }

        return [[
            'jumlahKemasan' => (int) $jumlah,
            'kodeJenisKemasan' => (string) $jenis,
            'seriKemasan' => 1,
            'merkKemasan' => (string) self::orDefault($header['merkKemasan'] ?? null, ''),
        ]];
    }

    // §9.6: hanya bila nomor kontainer terisi. kodeUkuranKontainer kosong = ANGKA 0 (Q-h, disalin).
    private function kontainer(array $header): array
    {
        if (self::orDefault($header['nomorKontainer'] ?? null, '') === '') {
            return [];
        }

        return [[
            'seriKontainer' => 1,
            'kodeTipeKontainer' => (string) self::orDefault($header['kodeTipeKontainer'] ?? null, ''),
            'kodeUkuranKontainer' => self::orDefault($header['kodeUkuranKontainer'] ?? null, 0),
            'nomorKontainer' => (string) $header['nomorKontainer'],
            'kodeJenisKontainer' => (string) self::orDefault($header['kodeJenisKontainer'] ?? null, ''),
        ]];
    }

    // §9.4 - key yang tidak ada di skema resmi barang (nilaiDiskon, volume, nilaiJasa, persen*) tidak dikirim.
    private function barang(array $row): array
    {
        $netto = $row['netto'] ?? null;
        $cifRupiah = round((float) ($row['cifRupiah'] ?? 0), 2);
        $satuan = $this->strOrNull($row['kodeSatuanBarang'] ?? null);

        return [
            'idBarang' => '0',
            'asuransi' => $this->fltOrNull($row['asuransi'] ?? null),
            'cif' => $this->fltOrNull($row['cif'] ?? null),
            'diskon' => 0,
            'fob' => (int) ($row['fob'] ?? 0), // bilangan bulat = fob header dipotong (Q-g)
            'freight' => $this->num($row['freight'] ?? null, 0),
            'hargaEkspor' => 0,
            'hargaPenyerahan' => 0,
            'hargaSatuan' => 0,
            'isiPerKemasan' => 0,
            'jumlahKemasan' => $this->round2($row['jumlahKemasan'] ?? null, 0.0),
            'jumlahSatuan' => $this->fltOrNull($row['jumlahSatuan'] ?? null),
            'kodeBarang' => $this->strOrNull($row['kodeBarang'] ?? null),
            'kodeDokumen' => '23',
            'kodeKategoriBarang' => $this->strOrNull($row['kodeKategoriBarang'] ?? null),
            'kodeJenisKemasan' => $this->strOrNull($row['kodeJenisKemasan'] ?? null),
            'kodeNegaraAsal' => $this->strOrNull($row['kodeNegaraAsal'] ?? null),
            'kodePerhitungan' => '0',
            'kodeSatuanBarang' => $satuan,
            'merk' => (string) self::orDefault($row['merk'] ?? null, '-'),
            'netto' => $this->fltOrNull($netto), // tidak dibulatkan
            'nilaiBarang' => $this->round2($row['cif'] ?? null, 0),
            'nilaiTambah' => $this->round2($row['cif'] ?? null, 0),
            'posTarif' => $this->strOrNull($row['posTarif'] ?? null),
            'seriBarang' => (int) ($row['seriBarang'] ?? 0),
            'spesifikasiLain' => (string) self::orDefault($row['spesifikasiLain'] ?? null, '0'),
            'tipe' => (string) self::orDefault($row['tipe'] ?? null, '0'),
            'ukuran' => (string) self::orDefault($row['ukuran'] ?? null, '0'),
            'uraian' => $this->strOrNull($row['uraian'] ?? null),
            'ndpbm' => $this->num($row['ndpbm'] ?? null, 0),
            'cifRupiah' => $cifRupiah,
            'hargaPerolehan' => 0,
            'kodeAsalBahanBaku' => '0',
            // Q-c: tarif BM 40 / PPh 2.5 / PPN 11 ditanam; nilaiBayar = CIF Rupiah penuh; jumlahSatuan
            // = netto TANPA pembulatan di ketiganya. Urutan key BM berbeda (kodeJenisPungutan sebelum
            // kodeSatuanBarang).
            'barangTarif' => [
                [
                    'kodeJenisTarif' => '1',
                    'jumlahSatuan' => $this->num($netto, 0),
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
                    'jumlahSatuan' => $this->num($netto, 0),
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
            'barangDokumen' => [],
        ];
    }

    // Selalu float (0.0 bila kosong): kolom root BC 2.3 memang float semua.
    private function flt($value): float
    {
        return (float) self::orDefault($value, 0);
    }

    // Float dibulatkan 2 desimal (skema: kelipatan 0.01); 0.0 bila kosong.
    private function flt2($value): float
    {
        return round((float) self::orDefault($value, 0), 2);
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
