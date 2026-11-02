<?php

namespace App\Http\Controllers\DocumentBc;

use App\Http\Controllers\Controller;
use App\Services\BeaCukai\Bc41PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use App\Services\BeaCukai\CeisaService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Dokumen BC 4.1 - barang keluar dari kawasan berikat ke daerah pabean (barang asal lokal), berdiri sendiri:
// tanpa Outstanding / dokumen sumber dan tanpa Jaminan (spesifikasi: docs/documentbc41_plan.md). Kembaran
// DocumentBc25Controller: header + 5 koleksi disimpan sekaligus dari satu JSON, dikirim ke CEISA lewat
// Bc41PayloadBuilder + CeisaService. Payload mengikuti skema RESMI docs/bc_schema/bc41.json (tanpa pungutan,
// bukti bayar, cara angkut, negara asal, tarif barang); ada Wajib Bayar (kodePembayar = seri entitas 3 / 7 / 8),
// Pengangkut (nama + nomor) dan Volume (per barang, total = jumlahnya). Nama tabel di config/beacukai.php, kolom di
// docs/update-db-DBSMLNEW/beacukai/16_tabel_documentbc41.sql dan 18_kolom_bc41_skema_resmi_dan_username_ceisa.sql.
//
// JS/payload memakai key camelCase (nama logis di spesifikasi), tabel memakai kolom UPPERCASE.
// Peta di bawah = satu-satunya tempat pemetaan keduanya. Tipe:
//   str   teks NOT NULL ('' bila kosong)      strn  teks nullable ('' → null)
//   dec   desimal NOT NULL (0 bila kosong)    decn  desimal nullable
//   int   bulat nullable                      intz  bulat NOT NULL (0 bila kosong)
//   date  'Y-m-d' atau null                   seri  posisi baris 1..n (diisi server)
// Elemen ketiga (opsional) = nilai bawaan bila kosong.
class DocumentBc41Controller extends Controller
{
    private const HEADER = [
        'nomorAju' => ['NOMORAJU', 'str'],
        'kodeKantor' => ['KODEKANTOR', 'str'],
        'kodeJenisTpb' => ['KODEJENISTPB', 'str', '1'],
        'kodeTujuanPengiriman' => ['KODETUJUANPENGIRIMAN', 'str'],
        'noRegBc' => ['NOREGBC', 'strn'],
        'tglBc' => ['TGLBC', 'date'],
        'arrDate' => ['ARRDATE', 'date'],
        // Transaksi: Harga. cifRupiah / DPP / PPN / PPnBM dihitung form; server mempercayainya (plan §5.2).
        'kodeValuta' => ['KODEVALUTA', 'str', 'IDR'],
        'ndpbm' => ['NDPBM', 'dec'],
        'cif' => ['CIF', 'dec'],
        'cifRupiah' => ['CIFRUPIAH', 'dec'],
        'hargaPenyerahan' => ['HARGAPENYERAHAN', 'dec'],
        // Transaksi: Pajak
        'uangMuka' => ['UANGMUKA', 'dec'],
        'diskon' => ['DISKON', 'dec'],
        'dasarPengenaanPajak' => ['DASARPENGENAANPAJAK', 'dec'],
        'tarifPpnPajak' => ['TARIFPPNPAJAK', 'dec'],
        'ppnPajak' => ['PPNPAJAK', 'dec'],
        'tarifPpnbmPajak' => ['TARIFPPNBMPAJAK', 'dec'],
        'ppnbmPajak' => ['PPNBMPAJAK', 'dec'],
        // Transaksi: Berat. Volume = jumlah volume barang (dihitung server saat simpan, bukan dipercaya dari form).
        'bruto' => ['BRUTO', 'dec'],
        'netto' => ['NETTO', 'dec'],
        'volume' => ['VOLUME', 'dec'],
        'namaPengangkut' => ['NAMAPENGANGKUT', 'str'],
        'nomorPengangkut' => ['NOMORPENGANGKUT', 'str'],
        'nomorIdentitasEntitas1' => ['NOMORIDENTITASENTITAS1', 'strn'],
        'namaEntitas1' => ['NAMAENTITAS1', 'str'],
        'alamatEntitas1' => ['ALAMATENTITAS1', 'str'],
        'nomorIjinEntitas1' => ['NOMORIJINENTITAS1', 'strn'],
        'tanggalIjinEntitas1' => ['TANGGALIJINENTITAS1', 'date'],
        'nomorIdentitasEntitas2' => ['NOMORIDENTITASENTITAS2', 'strn'],
        'namaEntitas2' => ['NAMAENTITAS2', 'str'],
        'alamatEntitas2' => ['ALAMATENTITAS2', 'str'],
        'nomorIdentitasEntitas3' => ['NOMORIDENTITASENTITAS3', 'strn'],
        'namaEntitas3' => ['NAMAENTITAS3', 'str'],
        'alamatEntitas3' => ['ALAMATENTITAS3', 'str'],
        'selectEntitas1' => ['SELECTENTITAS1', 'int'],
        'selectEntitas2' => ['SELECTENTITAS2', 'int'],
        'selectEntitas3' => ['SELECTENTITAS3', 'int'],
        // Wajib Bayar = seri entitas pembayar ('3' / '7' / '8').
        'kodePembayar' => ['KODEPEMBAYAR', 'strn'],
        'kotaTtd' => ['KOTATTD', 'str'],
        'tanggalTtd' => ['TANGGALTTD', 'date'],
        'namaTtd' => ['NAMATTD', 'str'],
        'jabatanTtd' => ['JABATANTTD', 'str'],
    ];

    // Koleksi anak: [key config tabel, kolom urutan baca, peta kolom].
    private const CHILDREN = [
        'barang' => ['bc41_barang', 'SERIBARANG', [
            'seriBarang' => ['SERIBARANG', 'seri'],
            'posTarif' => ['POSTARIF', 'str'],
            'kodeBarang' => ['KODEBARANG', 'str'],
            'uraian' => ['URAIAN', 'str'],
            'merk' => ['MERK', 'strn'],
            'tipe' => ['TIPE', 'strn'],
            'ukuran' => ['UKURAN', 'strn'],
            'spesifikasiLain' => ['SPESIFIKASILAIN', 'strn'],
            'kodeSatuanBarang' => ['KODESATUANBARANG', 'str'],
            'jumlahSatuan' => ['JUMLAHSATUAN', 'dec'],
            'jumlahKemasan' => ['JUMLAHKEMASAN', 'dec'],
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'strn'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '41'],
            'netto' => ['NETTO', 'dec'],
            'volume' => ['VOLUME', 'dec'],
            'cif' => ['CIF', 'dec'],
            'cifRupiah' => ['CIFRUPIAH', 'dec'],
            'ndpbm' => ['NDPBM', 'dec'],
            'hargaPenyerahan' => ['HARGAPENYERAHAN', 'decn'],
            'hargaPerolehan' => ['HARGAPEROLEHAN', 'decn'],
            'hargaEkspor' => ['HARGAEKSPOR', 'decn'],
            'isiPerKemasan' => ['ISIPERKEMASAN', 'decn'],
        ]],
        'bahanbaku' => ['bc41_bahanbaku', 'ID', [
            'seriBarang' => ['SERIBARANG', 'int'],
            'seriBahanBaku' => ['SERIBAHANBAKU', 'int'],
            'posTarif' => ['POSTARIF', 'str'],
            'kodeBarang' => ['KODEBARANG', 'str'],
            'uraianBarang' => ['URAIANBARANG', 'str'],
            'merkBarang' => ['MERKBARANG', 'strn'],
            'tipeBarang' => ['TIPEBARANG', 'strn'],
            'ukuranBarang' => ['UKURANBARANG', 'strn'],
            'spesifikasiLainBarang' => ['SPESIFIKASILAINBARANG', 'strn'],
            'kodeSatuanBarang' => ['KODESATUANBARANG', 'str'],
            'jumlahSatuan' => ['JUMLAHSATUAN', 'dec'],
            'netto' => ['NETTO', 'dec'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '41'],
            'kodeKantorAsal' => ['KODEKANTORASAL', 'str'],
            'nomorDaftarDokAsal' => ['NOMORDAFTARDOKASAL', 'str'],
            'tanggalDaftarDokAsal' => ['TANGGALDAFTARDOKASAL', 'date'],
            'nomorAjuDokAsal' => ['NOMORAJUDOKASAL', 'strn'],
            'seriBarangDokAsal' => ['SERIBARANGDOKASAL', 'int'],
            'cif' => ['CIF', 'dec'],
            'cifRupiah' => ['CIFRUPIAH', 'dec'],
            'hargaPenyerahan' => ['HARGAPENYERAHAN', 'dec'],
            'hargaPerolehan' => ['HARGAPEROLEHAN', 'dec'],
            'nilaiJasa' => ['NILAIJASA', 'dec'],
        ]],
        'kemasan' => ['bc41_kemasan', 'ID', [
            'seriKemasan' => ['SERIKEMASAN', 'seri'],
            'jumlahKemasan' => ['JUMLAHKEMASAN', 'intz'],
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'strn'],
            'merkKemasan' => ['MERKKEMASAN', 'str'],
        ]],
        'kontainer' => ['bc41_kontainer', 'SERIKONTAINER', [
            'seriKontainer' => ['SERIKONTAINER', 'seri'],
            'nomorKontainer' => ['NOMORKONTAINER', 'strn'],
            'kodeUkuranKontainer' => ['KODEUKURANKONTAINER', 'str'],
            'kodeJenisKontainer' => ['KODEJENISKONTAINER', 'str'],
            'kodeTipeKontainer' => ['KODETIPEKONTAINER', 'str'],
        ]],
        'dokumen' => ['bc41_dokumen', 'SERIDOKUMEN', [
            'seriDokumen' => ['SERIDOKUMEN', 'seri'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '41'],
            'nomorDokumen' => ['NOMORDOKUMEN', 'strn'],
            'tanggalDokumen' => ['TANGGALDOKUMEN', 'date'],
        ]],
    ];

    // Daftar kode tetap (docs/documentbc41_plan.md Lampiran A).
    private const KODE = [
        'jenisTpb' => ['1' => 'Kawasan Berikat'],
        'tujuanPengiriman' => ['1' => 'Penyerahan BKP', '2' => 'Penyerahan JKP', '3' => 'Retur', '4' => 'Non Penyerahan', '5' => 'Lainnya'],
        'jenisMuatan' => ['4' => 'Empty', '7' => 'LCL', '8' => 'FCL'],
        'dokumenAsal' => ['23' => 'BC 2.3', '27' => 'BC 2.7', '40' => 'BC 4.0'],
    ];

    // Salinan data master ENTITAS di dokumen (Q-f): [key camelCase => akhiran kolom]. Kolomnya
    // <akhiran><n>, mis. KODEJENISAPIENTITAS1. Diisi server saat simpan dari ENTITAS.id yang dipilih.
    private const SALINAN_ENTITAS = [
        'kodeJenisApi' => 'KODEJENISAPIENTITAS',
        'kodeJenisIdentitas' => 'KODEJENISIDENTITASENTITAS',
        'kodeStatus' => 'KODESTATUSENTITAS',
        'nibEntitas' => 'NIBENTITAS',
    ];

    // Pemeriksaan saat KIRIM terhadap skema resmi docs/bc_schema/bc41.json (bukan saat simpan). Kolom teks di bawah
    // ini tidak boleh kosong walau skema hanya menuntut tipe string (keputusan user). userPortal diperiksa terpisah
    // (masalahKirim) karena pesannya khusus. Yang boleh kosong: merk / tipe / ukuran / spesifikasi barang, merk kemasan,
    // bahan baku per barang, dan NIB Pemilik / Penerima (skema hanya menuntutnya di Pengusaha TPB).
    private const WAJIB_ISI = [
        'kodeKantor', 'kodeJenisTpb', 'kodeTujuanPengiriman', 'kodeLokasiBayar', 'kodePembayar', 'kotaTtd', 'namaTtd', 'jabatanTtd',
        'pengangkut.namaPengangkut', 'pengangkut.nomorPengangkut', 'kemasan.kodeJenisKemasan',
        'barang.kodeBarang', 'barang.uraian', 'barang.posTarif', 'barang.kodeSatuanBarang', 'barang.kodeJenisKemasan',
        'barang.bahanBaku.kodeBarang', 'barang.bahanBaku.posTarif', 'barang.bahanBaku.uraianBarang', 'barang.bahanBaku.kodeSatuanBarang',
        'barang.bahanBaku.kodeDokAsal', 'barang.bahanBaku.kodeKantor', 'barang.bahanBaku.nomorDaftarDokAsal', 'barang.bahanBaku.nomorAjuDokAsal',
        'entitas.namaEntitas', 'entitas.alamatEntitas', 'entitas.nomorIdentitas', 'entitas.nibEntitas', 'entitas.nomorIjinEntitas',
        'dokumen.kodeDokumen', 'dokumen.nomorDokumen', 'dokumen.tanggalDokumen',
    ];

    public function __construct(private Bc41PayloadBuilder $builder, private CeisaService $ceisa, private CeisaSchemaValidator $skema) {}

    public function index(Request $req)
    {
        $kodemenu = '';
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu, $req->path());
        if (! $akses || ! $akses->HASACCESS) {
            return redirect('/home');
        }

        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

        return view('documentbc.documentbc41', [
            'periode' => $periode,
            'menul0' => $menul0,
            'akses' => $akses,
            'kodeKantor' => config('beacukai.kode_kantor'),
            'kode' => self::KODE,
            'entitas' => $this->daftarEntitas(),
            // kurs = DBVALAS.KURS: tombol "Kurs terbaru" mengisi NDPBM dari sini (IDR selalu 1).
            'valuta' => DB::connection('SML')->select('SELECT KODEVLS AS kode, NAMAVLS AS nama, KURS AS kurs FROM dbVALAS ORDER BY KODEVLS'),
            'ukuranKontainer' => $this->daftarKontainer('ukuran_kontainer', 'KODEUKURANKONTAINER', 'NAMAUKURANKONTAINER'),
            'tipeKontainer' => $this->daftarKontainer('tipe_kontainer', 'KODETIPEKONTAINER', 'NAMATIPEKONTAINER'),
            'lookup' => [
                'satuan' => $this->daftarKode('satuan'),
                'kemasan' => $this->daftarKode('kemasan'),
                'jenisDokumen' => $this->daftarKode('jenis_dokumen'),
            ],
        ]);
    }

    // Daftar transaksi: semua BC 4.1, tanpa filter periode (plan §4.1). Tanggal = arr_date (Tanggal Pengeluaran).
    public function loadAll()
    {
        return DB::connection('SML')->select('
            SELECT ID AS id, NOMORAJU AS nomorAju, ARRDATE AS arrDate, NAMAENTITAS3 AS namaEntitas3,
                   ISNULL(SENT, 0) AS sent, TGLKIRIM AS tglKirim
            FROM '.$this->tabel('bc41').'
            ORDER BY ID DESC');
    }

    // Pemilih HS (POSTARIFHS): 50 kode pertama yang cocok. Tarif hanya informasi - tidak disalin ke barang.
    public function listHs(Request $req)
    {
        $hs = config('beacukai.tables.hs');
        if (! $hs || ! $hs['table']) {
            return [];
        }

        return DB::connection('SML')->select("
            SELECT TOP 50 {$hs['kode']} AS posTarif, PERSENBM AS persenBM, PERSENPPN AS persenPPN, PERSENPPh AS persenPPh
            FROM {$hs['table']}
            WHERE {$hs['kode']} LIKE :cari
            ORDER BY {$hs['kode']}", ['cari' => '%'.trim((string) $req->search).'%']);
    }

    public function spDetail(Request $req)
    {
        $doc = $this->muatDokumen((int) $req->id);
        if (! $doc) {
            return response()->json(['message' => 'Dokumen tidak ditemukan.', 'errors' => ['id' => 'Document not found.']], 404);
        }

        // Tanggal kosong dikirim '' supaya bisa langsung diisikan ke <input type="date">.
        $kosongkan = fn (array $row) => array_map(fn ($v) => $v ?? '', $row);
        $out = $kosongkan($doc['header']);
        foreach (array_keys(self::CHILDREN) as $nama) {
            $out[$nama] = array_map($kosongkan, $doc['children'][$nama]);
        }

        return $out;
    }

    public function spAdd(Request $req)
    {
        if ($tolak = $this->tolakAkses('ISTAMBAH')) {
            return $tolak;
        }
        $data = $this->bacaPayload($req);
        if (! $data) {
            return response()->json(['message' => 'Data tidak valid.'], 422);
        }

        $header = $this->normalisasi(self::HEADER, $data['header'] ?? []);
        if ($tolak = $this->validasiHeader($header)) {
            return $tolak;
        }
        $children = $this->normalisasiAnak($data);
        $header['volume'] = $this->jumlahVolume($children);

        return DB::connection('SML')->transaction(function () use ($header, $children) {
            $kolom = $this->keKolom(self::HEADER, $header) + $this->salinanEntitas($header) + [
                'USERBUAT' => \Auth::user()->username,
            ];
            $id = (int) $this->sisipkan($this->tabel('bc41'), $kolom, true);
            $this->sisipkanAnak($id, $children);

            return response()->json(['id' => $id, 'message' => 'Data telah ditambahkan.']);
        });
    }

    public function spEdit(Request $req)
    {
        if ($tolak = $this->tolakAkses('ISKOREKSI')) {
            return $tolak;
        }
        $data = $this->bacaPayload($req);
        $id = (int) ($data['id'] ?? 0);
        if (! $data || ! $id) {
            return response()->json(['message' => 'Data tidak valid.'], 422);
        }

        $header = $this->normalisasi(self::HEADER, $data['header'] ?? []);
        if ($tolak = $this->validasiHeader($header)) {
            return $tolak;
        }
        $children = $this->normalisasiAnak($data);
        $header['volume'] = $this->jumlahVolume($children);

        return DB::connection('SML')->transaction(function () use ($id, $header, $children) {
            if ($tolak = $this->cekBolehUbah($id)) {
                return $tolak;
            }

            // Header ditimpa (SENT tidak diubah), anak diganti seluruhnya.
            $kolom = $this->keKolom(self::HEADER, $header) + $this->salinanEntitas($header);
            $set = implode(', ', array_map(fn ($c) => "{$c} = ?", array_keys($kolom)));
            DB::connection('SML')->update(
                'UPDATE '.$this->tabel('bc41')." SET {$set}, USERUBAH = ?, TGLUBAH = GETDATE() WHERE ID = ?",
                array_merge(array_values($kolom), [\Auth::user()->username, $id])
            );
            $this->hapusAnak($id);
            $this->sisipkanAnak($id, $children);

            return response()->json(['id' => $id, 'message' => 'Data telah diubah.']);
        });
    }

    public function spDelete(Request $req)
    {
        if ($tolak = $this->tolakAkses('ISHAPUS')) {
            return $tolak;
        }
        $id = (int) $req->id;

        return DB::connection('SML')->transaction(function () use ($id) {
            if ($tolak = $this->cekBolehUbah($id)) {
                return $tolak;
            }
            $this->hapusAnak($id);
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel('bc41').' WHERE ID = ?', [$id]);

            return response()->json(['message' => 'Data telah dihapus.']);
        });
    }

    // Kirim ke CEISA (docs/documentbc41_plan.md §8; sama dengan BC 2.5).
    public function spKirim(Request $req)
    {
        if ($tolak = $this->tolakAkses('ISEXPORT')) {
            return $tolak;
        }
        $id = (int) $req->id;
        if (! $id) {
            return response()->json(['message' => 'ID dokumen tidak ditemukan.'], 400);
        }

        try {
            $token = $this->ceisa->validToken(\Auth::user()->username);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        if (! $token) {
            return response()->json(['message' => 'Token tidak valid atau sudah expired. Silakan login ulang.'], 401);
        }

        $payload = $this->susunPayload($id);
        if (! $payload) {
            return response()->json(['message' => 'Dokumen tidak ditemukan.'], 404);
        }

        // Dokumen harus lolos skema resmi CEISA dulu; semua masalah ditampilkan sekaligus (bukan saat simpan).
        if ($masalah = $this->masalahKirim($payload)) {
            return response()->json([
                'message' => 'Dokumen belum bisa dikirim: '.count($masalah).' masalah ditemukan. Perbaiki lalu kirim ulang.',
                'details' => $masalah,
            ], 422);
        }

        try {
            $res = $this->ceisa->send($token, $this->builder->encode($payload));
        } catch (ConnectionException $e) {
            return response()->json([
                'message' => 'Pengiriman gagal.', 'error' => $e->getMessage(), 'details' => [$e->getMessage()],
            ], 500);
        }

        $raw = $res->body();
        if ($res->failed()) {
            return response()->json([
                'message' => 'Pengiriman gagal.', 'error' => $raw, 'details' => CeisaService::details($raw),
            ], $res->status());
        }

        $json = json_decode($raw, true);
        if (! is_array($json)) {
            return response()->json([
                'message' => 'Response tidak valid.', 'raw' => $raw, 'details' => CeisaService::details($raw),
            ], 502);
        }

        if (($json['status'] ?? null) !== 'OK') {
            return response()->json([
                'message' => 'Pengiriman gagal.', 'details' => CeisaService::details($raw), 'response' => $json,
            ], 400);
        }

        $idHeader = CeisaService::cariNilai($json, ['idHeader']);
        DB::connection('SML')->update(
            'UPDATE '.$this->tabel('bc41').' SET SENT = 1, IDHEADER = ?, TGLKIRIM = GETDATE(), USERKIRIM = ? WHERE ID = ?',
            [$idHeader === null ? null : mb_substr((string) $idHeader, 0, 100), \Auth::user()->username, $id]
        );

        return response()->json([
            'message' => is_string($json['message'] ?? null) && $json['message'] !== '' ? $json['message'] : 'Data berhasil dikirim.',
            'idHeader' => $idHeader,
        ]);
    }

    // JSON yang AKAN dikirim ke CEISA (tanpa mengirim), untuk dicocokkan dengan Villiger.
    public function spPreview(Request $req)
    {
        if ($tolak = $this->tolakAkses('ISEXPORT')) {
            return $tolak;
        }
        $payload = $this->susunPayload((int) $req->id);
        if (! $payload) {
            return response()->json(['message' => 'Dokumen tidak ditemukan.'], 404);
        }

        // JSON selalu ditampilkan walau dokumen belum lengkap; masalah skema hanya jadi peringatan (tidak memblokir).
        return response()->json([
            'json' => $this->builder->encode($payload, true),
            'masalah' => $this->masalahKirim($payload),
        ]);
    }

    /* ---------------------------------------------------------------- helpers */

    private function tabel(string $key): string
    {
        $tabel = config("beacukai.tables.{$key}");
        if (! $tabel) {
            throw new RuntimeException("Tabel {$key} belum dikonfigurasi (config/beacukai.php).");
        }

        return $tabel;
    }

    // Hak akses dicek ulang di server; URL AJAX bukan href menu, jadi href ditulis langsung.
    private function tolakAkses(string $hak)
    {
        $akses = app('App\Http\Controllers\GlobalController')->getAkses('', 'documentbc41');
        if (! $akses || ! $akses->HASACCESS || ! $akses->$hak) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        return null;
    }

    // Skema resmi + aturan BC 4.1 di luar skema: minimal 1 barang, kemasan wajib (tiap baris: jenis terisi, jumlah > 0),
    // userPortal (username CEISA dari Login BC) harus ada. Dokumen pelengkap dan bahan baku tidak diwajibkan, tetapi
    // baris yang ada harus lengkap. nilaiJasa bahan baku di skema integer, tetapi boleh angka desimal (keputusan user).
    private function masalahKirim(array $payload): array
    {
        $masalah = $this->skema->masalah('bc41', $payload, [
            'bagian' => [
                'barang' => 'Barang', 'bahanBaku' => 'Bahan Baku', 'dokumen' => 'Dokumen', 'kemasan' => 'Kemasan', 'kontainer' => 'Peti Kemas',
                'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Penerima Barang'], 'pengangkut' => 'Pengangkut',
            ],
            'wajibIsi' => self::WAJIB_ISI,
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer', 'bahanBaku'],
            'angka' => ['barang.bahanBaku.nilaiJasa'],
        ]);

        if (! $payload['barang']) {
            $masalah[] = 'Barang: minimal 1 barang.';
        }
        if (! $payload['kemasan']) {
            $masalah[] = 'Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).';
        }
        foreach ($payload['kemasan'] as $i => $k) {
            if ($k['jumlahKemasan'] <= 0) {
                $masalah[] = 'Kemasan #'.($i + 1).' > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0';
            }
        }
        if (trim($payload['userPortal']) === '') {
            $masalah[] = 'User Portal CEISA (userPortal): belum ada. Klik Login BC sekali supaya username CEISA tersimpan.';
        }

        return $masalah;
    }

    // Validasi simpan: Nomor AJU wajib (Q-l), Berat Kotor > 0 dan Wajib Bayar (plan §5.1; 422, bukan 200 - Q-n).
    // Wajib Bayar = seri 3 / 7 / 8 dan entitas slotnya harus sudah dipilih serta punya NPWP di master (Q-f).
    private function validasiHeader(array $header)
    {
        $errors = [];
        if ($header['nomorAju'] === '') {
            $errors['nomorAju'] = 'Nomor AJU harus diisi.';
        }
        if ((float) $header['bruto'] <= 0) {
            $errors['bruto'] = 'Berat Kotor harus lebih besar dari 0';
        }
        $slot = ['3' => 1, '7' => 2, '8' => 3][(string) $header['kodePembayar']] ?? null;
        if ($slot === null) {
            $errors['kodePembayar'] = 'Wajib Bayar harus dipilih';
        } elseif (! $this->entitasPunyaNpwp($header["selectEntitas{$slot}"])) {
            $errors['kodePembayar'] = 'Wajib Bayar harus dipilih dari entitas yang punya NPWP.';
        }

        return $errors ? response()->json(['message' => reset($errors), 'errors' => $errors], 422) : null;
    }

    // Total Volume header = jumlah volume semua barang (4 desimal, sesuai skema).
    private function jumlahVolume(array $children): float
    {
        return round(array_sum(array_column($children['barang'], 'volume')), 4);
    }

    private function entitasPunyaNpwp(?int $idEntitas): bool
    {
        if (! $idEntitas) {
            return false;
        }
        $e = DB::connection('SML')->selectOne('SELECT NOMORIDENTITAS FROM '.$this->tabel('entitas').' WHERE id = ?', [$idEntitas]);

        return $e && trim((string) $e->NOMORIDENTITAS) !== '';
    }

    // Dokumen yang sudah terkirim ke CEISA dikunci dari Edit/Hapus (Q-i). Dipanggil di dalam
    // transaksi; UPDLOCK menahan baris sampai commit supaya tidak bentrok dengan pengiriman.
    private function cekBolehUbah(int $id)
    {
        $doc = DB::connection('SML')->selectOne(
            'SELECT ID, ISNULL(SENT, 0) AS SENT FROM '.$this->tabel('bc41').' WITH (UPDLOCK, HOLDLOCK) WHERE ID = ?', [$id]
        );
        if (! $doc) {
            return response()->json(['message' => 'Dokumen tidak ditemukan.', 'errors' => ['id' => 'Document not found.']], 404);
        }
        if ((int) $doc->SENT === 1) {
            return response()->json(['message' => 'Dokumen sudah dikirim ke CEISA, tidak bisa diubah atau dihapus.'], 403);
        }

        return null;
    }

    private function bacaPayload(Request $req): ?array
    {
        $data = json_decode((string) $req->input('data', ''), true);

        return is_array($data) ? $data : null;
    }

    private function daftarKode(string $key): array
    {
        $cfg = config("beacukai.tables.{$key}");
        if (! $cfg || ! $cfg['table']) {
            return [];
        }
        $nama = $cfg['nama'] ?: $cfg['kode'];

        return DB::connection('SML')->select("SELECT {$cfg['kode']} AS kode, {$nama} AS nama FROM {$cfg['table']} ORDER BY {$cfg['kode']}");
    }

    // Master Ukuran / Tipe Kontainer (tabel terpisah): [kode, nama] untuk combobox Peti Kemas.
    private function daftarKontainer(string $key, string $kode, string $nama): array
    {
        $tabel = config("beacukai.tables.{$key}");
        if (! $tabel) {
            return [];
        }

        return DB::connection('SML')->select("SELECT {$kode} AS kode, {$nama} AS nama FROM {$tabel} ORDER BY {$kode}");
    }

    private function daftarEntitas(): array
    {
        return DB::connection('SML')->select('
            SELECT id, NAMAENTITAS AS namaEntitas, ALAMATENTITAS AS alamatEntitas, NOMORIDENTITAS AS nomorIdentitas,
                   NOMORIJINIDENTITAS AS nomorIjinEntitas, CONVERT(varchar(10), TANGGALIJINIDENTITAS, 23) AS tanggalIjinEntitas
            FROM '.$this->tabel('entitas').'
            ORDER BY NAMAENTITAS');
    }

    // Kolom salinan master entitas (Q-f) untuk ketiga entitas: diambil dari ENTITAS.id yang dipilih
    // pada saat simpan. Entitas yang tidak dipilih → semua kolomnya NULL.
    private function salinanEntitas(array $header): array
    {
        $kolom = [];
        foreach ([1, 2, 3] as $n) {
            $e = null;
            if ($idEntitas = $header["selectEntitas{$n}"]) {
                $e = DB::connection('SML')->selectOne('
                    SELECT e.KODEJENISAPI, e.KODEJENISIDENTITAS, e.KODESTATUS, e.NIBENTITAS, e.NOMORIJINIDENTITAS,
                           CONVERT(varchar(10), e.TANGGALIJINIDENTITAS, 23) AS TANGGALIJIN
                    FROM '.$this->tabel('entitas').' e
                    WHERE e.id = ?', [$idEntitas]);
            }
            $isi = fn ($v) => $v === null || trim((string) $v) === '' ? null : trim((string) $v);
            $kolom["KODEJENISAPIENTITAS{$n}"] = $e ? $isi($e->KODEJENISAPI) : null;
            $kolom["KODEJENISIDENTITASENTITAS{$n}"] = $e ? $isi($e->KODEJENISIDENTITAS) : null;
            $kolom["KODESTATUSENTITAS{$n}"] = $e ? $isi($e->KODESTATUS) : null;
            $kolom["NIBENTITAS{$n}"] = $e ? $isi($e->NIBENTITAS) : null;

            // Izin entitas 2 (Pemilik Barang, wajib di skema) dan 3 tidak diketik di form: ikut disalin dari master.
            if ($n === 2 || $n === 3) {
                $kolom["NOMORIJINENTITAS{$n}"] = $e ? $isi($e->NOMORIJINIDENTITAS) : null;
                $kolom["TANGGALIJINENTITAS{$n}"] = $e ? $this->tanggal($e->TANGGALIJIN) : null;
            }
        }

        return $kolom;
    }

    // Header + koleksi anak dalam bentuk camelCase bertipe PHP (desimal float, tanggal 'Y-m-d').
    private function muatDokumen(int $id): ?array
    {
        $row = DB::connection('SML')->selectOne('SELECT * FROM '.$this->tabel('bc41').' WHERE ID = ?', [$id]);
        if (! $row) {
            return null;
        }
        $row = (array) $row;

        $header = [
            'id' => (int) $row['ID'],
            'sent' => (int) ($row['SENT'] ?? 0),
            'idHeader' => $row['IDHEADER'] ?? null,
        ] + $this->dariBaris(self::HEADER, $row);

        $children = [];
        foreach (self::CHILDREN as $nama => [$kunciTabel, $urut, $peta]) {
            $order = $urut === 'ID' ? 'ID' : "{$urut}, ID";
            $rows = DB::connection('SML')->select('SELECT * FROM '.$this->tabel($kunciTabel)." WHERE IDBC41 = ? ORDER BY {$order}", [$id]);
            $children[$nama] = array_map(fn ($r) => $this->dariBaris($peta, (array) $r), $rows);
        }

        return ['header' => $header, 'children' => $children, 'row' => $row];
    }

    private function susunPayload(int $id): ?array
    {
        $doc = $this->muatDokumen($id);
        if (! $doc) {
            return null;
        }

        // Entitas yang dikirim = isian dokumen (nama / alamat / nomor identitas / izin entitas 1) +
        // salinan master saat simpan (Q-f); tidak membaca master lagi.
        $row = $doc['row'];
        $h = $doc['header'];
        $entitas = [];
        foreach ([1, 2, 3] as $n) {
            if (! $h["selectEntitas{$n}"] && trim($h["namaEntitas{$n}"]) === '') {
                $entitas[] = null;

                continue;
            }
            $e = [
                'namaEntitas' => $h["namaEntitas{$n}"],
                'alamatEntitas' => $h["alamatEntitas{$n}"],
                'nomorIdentitas' => $h["nomorIdentitasEntitas{$n}"],
            ];
            foreach (self::SALINAN_ENTITAS as $key => $awalan) {
                $e[$key] = $row["{$awalan}{$n}"] ?? null;
            }
            $entitas[] = $e;
        }
        // Izin: Pengusaha TPB (1) = isian form; Pemilik Barang (2) = salinan master saat simpan (wajib di skema).
        if ($entitas[0]) {
            $entitas[0]['nomorIjinEntitas'] = $h['nomorIjinEntitas1'] ?? null;
            $entitas[0]['tanggalIjinEntitas'] = $h['tanggalIjinEntitas1'] ?? null;
        }
        if ($entitas[1]) {
            $entitas[1]['nomorIjinEntitas'] = $row['NOMORIJINENTITAS2'] ?? null;
            $entitas[1]['tanggalIjinEntitas'] = isset($row['TANGGALIJINENTITAS2']) ? substr((string) $row['TANGGALIJINENTITAS2'], 0, 10) : null;
        }

        // userPortal = username CEISA dari Login BC terakhir user ini (bukan username ERP).
        $header = $doc['header'] + ['userPortal' => (string) $this->ceisa->username(\Auth::user()->username)];

        return $this->builder->build($header, $doc['children'], $entitas);
    }

    // §5.2 - normalisasi isian dari UI sesuai tipe kolom.
    private function normalisasi(array $peta, array $input, ?int $posisi = null): array
    {
        $out = [];
        foreach ($peta as $key => $def) {
            [, $tipe] = $def;
            $v = $input[$key] ?? null;
            $out[$key] = match ($tipe) {
                'seri' => $posisi,
                'str' => ($s = trim((string) $v)) === '' ? ($def[2] ?? '') : $s,
                'strn' => ($s = trim((string) $v)) === '' ? null : $s,
                'dec' => $this->desimal($v) ?? 0.0,
                'decn' => $this->desimal($v),
                'int' => $this->bulat($v),
                'intz' => $this->bulat($v) ?? 0,
                'date' => $this->tanggal($v),
            };
        }

        return $out;
    }

    private function normalisasiAnak(array $data): array
    {
        $children = [];
        foreach (self::CHILDREN as $nama => [, , $peta]) {
            $rows = is_array($data[$nama] ?? null) ? array_values($data[$nama]) : [];
            $children[$nama] = [];
            foreach ($rows as $i => $row) {
                $children[$nama][] = $this->normalisasi($peta, is_array($row) ? $row : [], $i + 1);
            }
        }

        // Bahan baku: seriBarang kosong → urutan baris; seriBahanBaku kosong → urutan di dalam
        // barangnya (bukan urutan global).
        $perBarang = [];
        foreach ($children['bahanbaku'] as $i => &$bb) {
            $bb['seriBarang'] ??= $i + 1;
            $perBarang[$bb['seriBarang']] = ($perBarang[$bb['seriBarang']] ?? 0) + 1;
            $bb['seriBahanBaku'] ??= $perBarang[$bb['seriBarang']];
        }
        unset($bb);

        return $children;
    }

    // Baris DB (UPPERCASE) → camelCase bertipe PHP.
    private function dariBaris(array $peta, array $row): array
    {
        $out = [];
        foreach ($peta as $key => [$kolom, $tipe]) {
            $v = $row[$kolom] ?? null;
            $out[$key] = match ($tipe) {
                'seri', 'int', 'intz' => $v === null ? null : (int) $v,
                'str' => (string) ($v ?? ''),
                'strn' => $v === null ? null : (string) $v,
                'dec' => (float) ($v ?? 0),
                'decn' => $v === null ? null : (float) $v,
                'date' => $v === null ? null : substr((string) $v, 0, 10),
            };
        }

        return $out;
    }

    private function keKolom(array $peta, array $nilai): array
    {
        $out = [];
        foreach ($peta as $key => [$kolom]) {
            $out[$kolom] = $nilai[$key];
        }

        return $out;
    }

    // INSERT dengan placeholder posisi (?) - pdo_sqlsrv tidak bisa memakai named param yang sama dua kali.
    private function sisipkan(string $tabel, array $kolom, bool $kembalikanId = false)
    {
        $daftar = implode(', ', array_keys($kolom));
        $tanda = implode(', ', array_fill(0, count($kolom), '?'));
        if ($kembalikanId) {
            return DB::connection('SML')->selectOne("INSERT INTO {$tabel} ({$daftar}) OUTPUT INSERTED.ID VALUES ({$tanda})", array_values($kolom), false)->ID;
        }
        DB::connection('SML')->insert("INSERT INTO {$tabel} ({$daftar}) VALUES ({$tanda})", array_values($kolom));
    }

    private function sisipkanAnak(int $id, array $children): void
    {
        foreach (self::CHILDREN as $nama => [$kunciTabel, , $peta]) {
            foreach ($children[$nama] as $row) {
                $this->sisipkan($this->tabel($kunciTabel), ['IDBC41' => $id] + $this->keKolom($peta, $row));
            }
        }
    }

    private function hapusAnak(int $id): void
    {
        foreach (self::CHILDREN as [$kunciTabel]) {
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel($kunciTabel).' WHERE IDBC41 = ?', [$id]);
        }
    }

    // "2,500,000.00" → 2500000.0; kosong / bukan angka → null.
    private function desimal($v): ?float
    {
        if ($v === null || is_bool($v) || is_array($v)) {
            return null;
        }
        $v = str_replace(',', '', trim((string) $v));

        return is_numeric($v) ? (float) $v : null;
    }

    // Lewat float lalu dipotong ("3.0" → 3); kosong / bukan angka → null.
    private function bulat($v): ?int
    {
        $f = $this->desimal($v);

        return $f === null ? null : (int) $f;
    }

    private function tanggal($v): ?string
    {
        $v = trim((string) $v);
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v, $m) || ! checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return null;
        }

        return $v;
    }
}
