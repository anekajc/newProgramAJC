<?php

namespace App\Services\BeaCukai;

use RuntimeException;

// Pemeriksa payload CEISA terhadap skema resmi (docs/bc_schema/bcNN.json, JSON Schema draft-07 versi CEISA).
// Dipakai saat KIRIM (bukan saat simpan): hasilnya daftar masalah berbahasa Indonesia yang ditampilkan ke user
// sebelum payload dikirim ke gateway. Hanya kata kunci yang dipakai skema CEISA yang didukung: type, required,
// properties, items (daftar skema = per posisi / tuple), enum, const, pattern, format date, multipleOf,
// maxlength (angka = jumlah digit) dan dependencies.
//
// Aturan di luar skema yang disepakati dengan user dikirim lewat $opsi:
//   bagian    nama array => label ("Barang") atau daftar label per posisi (entitas, tarif)
//   wajibIsi  jalur kolom (tanpa nomor baris, mis. "barang.kodeNegaraAsal") yang tidak boleh berisi "" walau
//             skema hanya menuntut tipe string
//   sisa      nama array yang skemanya hanya mendefinisikan baris pertama; baris berikutnya memakai skema
//             terakhir (mis. barang, dokumen lain-lain)
//   angka     jalur kolom (seperti wajibIsi) yang di skema bertipe integer tetapi keputusan user boleh berupa
//             angka desimal (mis. "barang.bahanBaku.nilaiJasa"); diperiksa sebagai number, bukan integer
class CeisaSchemaValidator
{
    private const POLA = [
        '^[A-Za-z0-9]{26}$' => 'harus tepat 26 karakter huruf/angka',
        '^[A-Z]{2}$' => 'harus 2 huruf kapital (kode negara, mis. MY)',
        '^[A-Za-z]{2}$' => 'harus 2 huruf (kode negara, mis. MY)',
    ];

    // Label kolom CEISA untuk pesan. Kolom yang belum terdaftar tampil dengan nama key-nya.
    private const LABEL = [
        'asuransi' => 'Asuransi', 'bruto' => 'Berat Kotor (Bruto)', 'cif' => 'Nilai CIF', 'fob' => 'FOB', 'freight' => 'Freight',
        'hargaPenyerahan' => 'Harga Penyerahan', 'jabatanTtd' => 'Jabatan (Pernyataan)', 'jumlahKontainer' => 'Jumlah Kontainer',
        'kodeAsuransi' => 'Kode Asuransi', 'kodeIncoterm' => 'Incoterm', 'kodeKantor' => 'Kantor Pabean',
        'kodeKantorBongkar' => 'Kantor Pabean Bongkar', 'kodePelBongkar' => 'Pelabuhan Bongkar', 'kodePelMuat' => 'Pelabuhan Muat',
        'kodePelTransit' => 'Pelabuhan Transit', 'kodeTps' => 'Tempat Penimbunan (TPS)', 'kodeTujuanTpb' => 'Tujuan TPB',
        'kodeTutupPu' => 'Tutup PU', 'kodeValuta' => 'Valuta', 'kotaTtd' => 'Kota (Pernyataan)', 'namaTtd' => 'Nama (Pernyataan)',
        'ndpbm' => 'NDPBM', 'netto' => 'Berat Bersih (Netto)', 'nik' => 'NIK', 'nilaiBarang' => 'Nilai Barang',
        'nomorAju' => 'Nomor AJU', 'nomorBc11' => 'Nomor BC 1.1', 'posBc11' => 'Pos BC 1.1', 'subposBc11' => 'Sub Pos BC 1.1',
        'tanggalBc11' => 'Tanggal BC 1.1', 'tanggalTiba' => 'Tanggal Tiba', 'tanggalTtd' => 'Tanggal (Pernyataan)',
        'biayaTambahan' => 'Biaya Tambahan', 'biayaPengurang' => 'Biaya Pengurang', 'kodeKenaPajak' => 'Kode Kena Pajak',
        'diskon' => 'Diskon', 'hargaEkspor' => 'Harga Ekspor', 'hargaSatuan' => 'Harga Satuan', 'isiPerKemasan' => 'Isi per Kemasan',
        'jumlahKemasan' => 'Jumlah Kemasan', 'jumlahSatuan' => 'Jumlah Satuan', 'kodeBarang' => 'Kode Barang',
        'kodeKategoriBarang' => 'Kategori Barang', 'kodeJenisKemasan' => 'Jenis Kemasan', 'kodeNegaraAsal' => 'Negara Asal',
        'kodePerhitungan' => 'Kode Perhitungan', 'kodeSatuanBarang' => 'Satuan', 'merk' => 'Merk', 'nilaiTambah' => 'Nilai Tambah',
        'posTarif' => 'Pos Tarif / HS', 'seriBarang' => 'Seri Barang', 'spesifikasiLain' => 'Spesifikasi Lain', 'tipe' => 'Tipe',
        'ukuran' => 'Ukuran', 'uraian' => 'Uraian', 'cifRupiah' => 'CIF Rupiah', 'hargaPerolehan' => 'Harga Perolehan',
        'kodeAsalBahanBaku' => 'Asal Bahan Baku', 'kodeJenisTarif' => 'Jenis Tarif', 'kodeFasilitasTarif' => 'Fasilitas Tarif',
        'kodeJenisPungutan' => 'Jenis Pungutan', 'nilaiBayar' => 'Nilai Bayar', 'nilaiFasilitas' => 'Nilai Fasilitas',
        'nilaiSudahDilunasi' => 'Nilai Sudah Dilunasi', 'tarif' => 'Tarif', 'tarifFasilitas' => 'Tarif Fasilitas',
        'alamatEntitas' => 'Alamat', 'kodeEntitas' => 'Kode Entitas', 'kodeJenisIdentitas' => 'Jenis Identitas', 'namaEntitas' => 'Nama',
        'nibEntitas' => 'NIB', 'nomorIdentitas' => 'NPWP / Identitas', 'nomorIjinEntitas' => 'Nomor Izin',
        'tanggalIjinEntitas' => 'Tanggal Izin', 'kodeNegara' => 'Negara', 'kodeJenisApi' => 'Jenis API', 'kodeStatus' => 'Status',
        'seriEntitas' => 'Seri Entitas', 'merkKemasan' => 'Merk Kemasan', 'seriKemasan' => 'Seri Kemasan',
        'kodeTipeKontainer' => 'Tipe', 'kodeUkuranKontainer' => 'Ukuran', 'nomorKontainer' => 'Nomor Peti Kemas',
        'seriKontainer' => 'Seri Peti Kemas', 'kodeJenisKontainer' => 'Jenis Muatan', 'kodeDokumen' => 'Jenis Dokumen',
        'nomorDokumen' => 'Nomor', 'seriDokumen' => 'Seri Dokumen', 'tanggalDokumen' => 'Tanggal', 'kodeBendera' => 'Bendera',
        'namaPengangkut' => 'Nama Sarana Pengangkut', 'nomorPengangkut' => 'Nomor Sarana Pengangkut', 'kodeCaraAngkut' => 'Cara Pengangkutan',
        'seriPengangkut' => 'Seri Pengangkut',
        // BC 2.5
        'idPengguna' => 'ID Pengguna', 'volume' => 'Total Volume', 'disclaimer' => 'Disclaimer', 'kodeCaraBayar' => 'Cara Bayar',
        'kodeLokasiBayar' => 'Lokasi Bayar', 'kodeJenisTpb' => 'Jenis TPB', 'kodeTujuanPengiriman' => 'Tujuan Pengiriman',
        'tanggalAju' => 'Tanggal Aju', 'dasarPengenaanPajak' => 'DPP', 'ppnPajak' => 'PPN', 'ppnbmPajak' => 'PPnBM',
        'tarifPpnPajak' => 'Tarif PPN', 'tarifPpnbmPajak' => 'Tarif PPnBM', 'kodeGunaBarang' => 'Guna Barang',
        'kodeKondisiBarang' => 'Kondisi Barang', 'kodeDokAsal' => 'Dokumen Asal', 'niperEntitas' => 'NIPER',
        'bahanBakuTarif' => 'Tarif Bahan Baku', 'merkBarang' => 'Merk', 'tipeBarang' => 'Tipe', 'ukuranBarang' => 'Ukuran',
        'uraianBarang' => 'Uraian', 'spesifikasiLainBarang' => 'Spesifikasi Lain', 'nomorAjuDokAsal' => 'Nomor AJU Dokumen Asal',
        'nomorDaftarDokAsal' => 'Nomor Daftar Dokumen Asal', 'tanggalDaftarDokAsal' => 'Tanggal Daftar Dokumen Asal',
        'seriBahanBaku' => 'Seri Bahan Baku', 'seriBarangDokAsal' => 'Seri Barang Dokumen Asal', 'seriIjin' => 'Seri Izin',
        // BC 4.0
        'asalData' => 'Asal Data', 'seri' => 'Seri Dokumen', 'uangMuka' => 'Uang Muka', 'nilaiJasa' => 'Nilai Jasa', 'vd' => 'VD',
        'jumlahRealisasi' => 'Jumlah Realisasi', 'nilaiPungutan' => 'Nilai Pungutan',
        // BC 4.1
        'userPortal' => 'User Portal CEISA', 'kodePembayar' => 'Wajib Bayar', 'idPengangkut' => 'ID Pengangkut', 'idDokumen' => 'ID Dokumen',
        // BC 2.6.1
        'kodeAsalBarang' => 'Asal Barang', 'idPungutan' => 'ID Pungutan', 'idJaminan' => 'ID Jaminan', 'kodeJenisJaminan' => 'Jenis Jaminan',
        'nomorJaminan' => 'Nomor Jaminan', 'tanggalJaminan' => 'Tanggal Jaminan', 'tanggalJatuhTempo' => 'Jatuh Tempo',
        'nilaiJaminan' => 'Nilai Jaminan', 'penjamin' => 'Penjamin', 'nomorBpj' => 'Nomor BPJ', 'tanggalBpj' => 'Tanggal BPJ',
    ];

    private string $dir;

    private array $out = [];

    private array $opsi = [];

    public function __construct(?string $dir = null)
    {
        $this->dir = rtrim($dir ?? dirname(__DIR__, 3).'/docs/bc_schema', '/\\');
    }

    /**
     * @param  string  $skema  nama berkas skema tanpa .json, mis. "bc23"
     * @param  array  $payload  payload hasil builder (array PHP, belum di-encode)
     * @return list<string> daftar masalah; kosong = lolos
     */
    public function masalah(string $skema, array $payload, array $opsi = []): array
    {
        $berkas = "{$this->dir}/{$skema}.json";
        if (! is_file($berkas)) {
            throw new RuntimeException("Skema CEISA {$skema} tidak ditemukan ({$berkas}).");
        }
        $schema = json_decode((string) file_get_contents($berkas), true);
        if (! is_array($schema)) {
            throw new RuntimeException("Skema CEISA {$skema} bukan JSON yang valid.");
        }

        $this->out = [];
        $this->opsi = $opsi + ['bagian' => [], 'wajibIsi' => [], 'sisa' => [], 'angka' => []];
        $this->cek($schema, $payload, []);

        return array_values(array_unique($this->out));
    }

    private function cek(array $schema, $nilai, array $jalur): void
    {
        $tipe = $schema['type'] ?? null;

        if ($nilai === null) {
            $this->masalahDi($jalur, 'wajib diisi');

            return;
        }

        if ($tipe === 'object') {
            if (! is_array($nilai) || array_is_list($nilai) && $nilai !== []) {
                $this->masalahDi($jalur, 'bentuk data tidak sesuai (harus objek)');

                return;
            }
            $wajib = $schema['required'] ?? [];
            foreach ($schema['properties'] ?? [] as $kunci => $sub) {
                if (! array_key_exists($kunci, $nilai)) {
                    if (in_array($kunci, $wajib, true)) {
                        $this->masalahDi(array_merge($jalur, [$kunci]), 'wajib diisi');
                    }

                    continue;
                }
                $this->cek($sub, $nilai[$kunci], array_merge($jalur, [$kunci]));
            }
            foreach ($schema['dependencies'] ?? [] as $kunci => $butuh) {
                if (! array_key_exists($kunci, $nilai) || ! is_array($butuh)) {
                    continue;
                }
                foreach ($butuh as $dep) {
                    if (! isset($nilai[$dep])) {
                        $this->masalahDi(array_merge($jalur, [$dep]), 'wajib diisi (data ini ikut dikirim)');
                    }
                }
            }

            return;
        }

        if ($tipe === 'array') {
            if (! is_array($nilai) || ! array_is_list($nilai)) {
                $this->masalahDi($jalur, 'bentuk data tidak sesuai (harus daftar)');

                return;
            }
            $items = $schema['items'] ?? null;
            if (! is_array($items)) {
                return;
            }
            $tuple = array_is_list($items) ? $items : [$items];
            $nama = (string) end($jalur);
            foreach ($nilai as $i => $baris) {
                $sub = $tuple[$i] ?? (in_array($nama, $this->opsi['sisa'], true) ? end($tuple) : null);
                if (is_array($sub)) {
                    $this->cek($sub, $baris, array_merge($jalur, [$i]));
                }
            }

            return;
        }

        $this->cekSkalar($schema, $nilai, $jalur);
    }

    private function cekSkalar(array $schema, $nilai, array $jalur): void
    {
        $tipe = $schema['type'] ?? null;
        if ($tipe === 'integer' && in_array($this->jalurDatar($jalur), $this->opsi['angka'], true)) {
            $tipe = 'number';
        }

        if ($tipe === 'string' && ! is_string($nilai)) {
            $this->masalahDi($jalur, 'harus berupa teks');

            return;
        }
        if ($tipe === 'number' && (! (is_int($nilai) || is_float($nilai)))) {
            $this->masalahDi($jalur, 'harus berupa angka');

            return;
        }
        if ($tipe === 'integer' && ! (is_int($nilai) || (is_float($nilai) && floor($nilai) == $nilai))) {
            $this->masalahDi($jalur, 'harus berupa bilangan bulat');

            return;
        }

        if (is_string($nilai)) {
            $kosong = trim($nilai) === '';
            if ($kosong && (in_array($this->jalurDatar($jalur), $this->opsi['wajibIsi'], true) || isset($schema['enum']) || isset($schema['pattern']) || isset($schema['format']))) {
                $this->masalahDi($jalur, isset($schema['enum']) ? 'wajib dipilih' : 'wajib diisi');

                return;
            }
            if (isset($schema['const']) && $nilai !== (string) $schema['const']) {
                $this->masalahDi($jalur, "harus berisi '{$schema['const']}'".($nilai === '' ? '' : " (sekarang '{$nilai}')"));
            }
            if (isset($schema['enum']) && ! in_array($nilai, array_map('strval', $schema['enum']), true)) {
                $this->masalahDi($jalur, 'harus salah satu dari: '.implode(', ', $schema['enum'])." (sekarang '{$nilai}')");
            }
            if (isset($schema['pattern']) && ! preg_match('~'.$schema['pattern'].'~', $nilai)) {
                $pesan = self::POLA[$schema['pattern']] ?? 'format tidak sesuai';
                $this->masalahDi($jalur, $pesan.' (sekarang '.strlen($nilai)." karakter: '{$nilai}')");
            }
            if (($schema['format'] ?? null) === 'date' && ! $this->tanggalValid($nilai)) {
                $this->masalahDi($jalur, "harus tanggal yang valid (YYYY-MM-DD), sekarang '{$nilai}'");
            }
            if (isset($schema['maxlength']) && mb_strlen($nilai) > (int) $schema['maxlength']) {
                $this->masalahDi($jalur, 'maksimal '.$schema['maxlength'].' karakter');
            }

            return;
        }

        if (isset($schema['multipleOf'])) {
            $desimal = $this->desimal((float) $schema['multipleOf']);
            $skala = (float) $nilai * (10 ** $desimal);
            if (abs($skala - round($skala)) > 1e-4) {
                $this->masalahDi($jalur, "maksimal {$desimal} angka di belakang koma (sekarang {$nilai})");
            }
        }
        if (isset($schema['maxlength'])) {
            $teks = rtrim(rtrim(sprintf('%.4F', abs((float) $nilai)), '0'), '.');
            $digit = strlen(preg_replace('/\D/', '', $teks));
            if ($digit > (int) $schema['maxlength']) {
                $this->masalahDi($jalur, "maksimal {$schema['maxlength']} digit (sekarang {$digit})");
            }
        }
    }

    // 0.01 -> 2, 0.0001 -> 4.
    private function desimal(float $kelipatan): int
    {
        $teks = rtrim(sprintf('%.8F', $kelipatan), '0');

        return max(0, strlen($teks) - strpos($teks, '.') - 1);
    }

    private function tanggalValid(string $v): bool
    {
        return preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v, $m) === 1 && checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    // Jalur tanpa nomor baris: ['barang', 0, 'kodeNegaraAsal'] -> "barang.kodeNegaraAsal".
    private function jalurDatar(array $jalur): string
    {
        return implode('.', array_filter($jalur, fn ($s) => ! is_int($s)));
    }

    private function masalahDi(array $jalur, string $pesan): void
    {
        $this->out[] = $this->nama($jalur).': '.$pesan;
    }

    // "Barang #1 > Negara Asal (kodeNegaraAsal)".
    private function nama(array $jalur): string
    {
        if ($jalur === []) {
            return 'Dokumen';
        }
        $bagian = [];
        $n = count($jalur);
        for ($i = 0; $i < $n; $i++) {
            $seg = $jalur[$i];
            $berikut = $jalur[$i + 1] ?? null;
            if (is_string($seg) && is_int($berikut)) {
                $label = $this->opsi['bagian'][$seg] ?? ucfirst($seg);
                $bagian[] = is_array($label) ? ($label[$berikut] ?? ucfirst($seg).' #'.($berikut + 1)) : $label.' #'.($berikut + 1);
                $i++;
            } elseif (is_string($seg) && $i === $n - 1) {
                $bagian[] = (self::LABEL[$seg] ?? $seg).(isset(self::LABEL[$seg]) ? " ({$seg})" : '');
            } elseif (is_string($seg)) {
                $bagian[] = $this->opsi['bagian'][$seg] ?? $seg;
            }
        }

        return implode(' > ', $bagian);
    }
}
