<?php

namespace App\Http\Controllers\DocumentBc;

use App\Http\Controllers\Controller;
use App\Services\BeaCukai\Bc25PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use App\Services\BeaCukai\CeisaService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Dokumen BC 2.5 - barang keluar dari kawasan berikat ke dalam negeri (TLDDP), berdiri sendiri: tanpa
// Outstanding / dokumen sumber dan tanpa Jaminan (spesifikasi: docs/documentbc25_plan.md). Mirip
// DocumentBc262Controller: header + 5 koleksi disimpan sekaligus dari satu JSON, dikirim ke CEISA lewat
// Bc25PayloadBuilder + CeisaService. Nama tabel di config/beacukai.php, kolom di
// docs/update-db-DBSMLNEW/beacukai/14_tabel_documentbc25.sql.
//
// JS/payload memakai key camelCase (nama logis di spesifikasi), tabel memakai kolom UPPERCASE.
// Peta di bawah = satu-satunya tempat pemetaan keduanya. Tipe:
//   str   teks NOT NULL ('' bila kosong)      strn  teks nullable ('' → null)
//   dec   desimal NOT NULL (0 bila kosong)    decn  desimal nullable
//   int   bulat nullable                      intz  bulat NOT NULL (0 bila kosong)
//   date  'Y-m-d' atau null                   seri  posisi baris 1..n (diisi server)
// Elemen ketiga (opsional) = nilai bawaan bila kosong.
class DocumentBc25Controller extends Controller
{
    private const HEADER = [
        'nomorAju' => ['NOMORAJU', 'str'],
        'kodeKantor' => ['KODEKANTOR', 'str'],
        'kodeJenisTpb' => ['KODEJENISTPB', 'str', '1'],
        'kodeTujuanPengiriman' => ['KODETUJUANPENGIRIMAN', 'str'],
        'kodeCaraBayar' => ['KODECARABAYAR', 'str', '1'],
        'kodeCaraAngkut' => ['KODECARAANGKUT', 'str'],
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
        // Transaksi: Berat + Total Volume (wajib di skema resmi)
        'bruto' => ['BRUTO', 'dec'],
        'netto' => ['NETTO', 'dec'],
        'volume' => ['VOLUME', 'dec'],
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
        'kotaTtd' => ['KOTATTD', 'str'],
        'tanggalTtd' => ['TANGGALTTD', 'date'],
        'namaTtd' => ['NAMATTD', 'str'],
        'jabatanTtd' => ['JABATANTTD', 'str'],
    ];

    // Koleksi anak: [key config tabel, kolom urutan baca, peta kolom].
    private const CHILDREN = [
        'barang' => ['bc25_barang', 'SERIBARANG', [
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
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '25'],
            'netto' => ['NETTO', 'dec'],
            'cif' => ['CIF', 'dec'],
            'cifRupiah' => ['CIFRUPIAH', 'dec'],
            'ndpbm' => ['NDPBM', 'dec'],
            'kodeKategoriBarang' => ['KODEKATEGORIBARANG', 'strn'],
            'kodeGunaBarang' => ['KODEGUNABARANG', 'strn'],
            'kodeKondisiBarang' => ['KODEKONDISIBARANG', 'str', '1'],
            'kodeDokAsal' => ['KODEDOKASAL', 'str', '23'],
            'keteranganDokumen' => ['KETERANGANDOKUMEN', 'str'],
            'persenBM' => ['PERSENBM', 'dec'],
            'persenPPN' => ['PERSENPPN', 'dec'],
            'persenPPh' => ['PERSENPPH', 'dec'],
            'hargaPenyerahan' => ['HARGAPENYERAHAN', 'decn'],
            'hargaPerolehan' => ['HARGAPEROLEHAN', 'decn'],
            'hargaEkspor' => ['HARGAEKSPOR', 'decn'],
            'hargaSatuan' => ['HARGASATUAN', 'decn'],
            'diskon' => ['DISKON', 'decn'],
            'freight' => ['FREIGHT', 'decn'],
            'isiPerKemasan' => ['ISIPERKEMASAN', 'decn'],
            'kodePerhitungan' => ['KODEPERHITUNGAN', 'strn'],
        ]],
        'bahanbaku' => ['bc25_bahanbaku', 'ID', [
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
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '25'],
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
        'kemasan' => ['bc25_kemasan', 'ID', [
            'seriKemasan' => ['SERIKEMASAN', 'seri'],
            'jumlahKemasan' => ['JUMLAHKEMASAN', 'intz'],
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'strn'],
            'merkKemasan' => ['MERKKEMASAN', 'str'],
        ]],
        'kontainer' => ['bc25_kontainer', 'SERIKONTAINER', [
            'seriKontainer' => ['SERIKONTAINER', 'seri'],
            'nomorKontainer' => ['NOMORKONTAINER', 'strn'],
            'kodeUkuranKontainer' => ['KODEUKURANKONTAINER', 'str'],
            'kodeJenisKontainer' => ['KODEJENISKONTAINER', 'str'],
            'kodeTipeKontainer' => ['KODETIPEKONTAINER', 'str'],
        ]],
        'dokumen' => ['bc25_dokumen', 'SERIDOKUMEN', [
            'seriDokumen' => ['SERIDOKUMEN', 'seri'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '25'],
            'nomorDokumen' => ['NOMORDOKUMEN', 'strn'],
            'tanggalDokumen' => ['TANGGALDOKUMEN', 'date'],
        ]],
    ];

    // Daftar kode tetap (docs/documentbc25_plan.md Lampiran A).
    private const KODE = [
        'jenisTpb' => ['1' => 'Kawasan Berikat'],
        'tujuanPengiriman' => ['1' => 'Penyerahan BKP', '2' => 'Penyerahan JKP', '3' => 'Retur', '4' => 'Non Penyerahan', '5' => 'Lainnya'],
        'caraBayar' => ['1' => 'Biasa/Tunai'],
        'caraAngkut' => ['1' => 'Laut', '2' => 'Kereta Api', '3' => 'Darat', '4' => 'Udara', '5' => 'Pos', '6' => 'Multimoda',
            '7' => 'Instalasi/Pipa', '8' => 'Perairan', '9' => 'Lainnya'],
        'jenisMuatan' => ['4' => 'Empty', '7' => 'LCL', '8' => 'FCL'],
        'dokumenAsal' => ['23' => 'BC 2.3', '27' => 'BC 2.7', '40' => 'BC 4.0'],
        'gunaBarang' => ['0' => 'Barang Berhubungan Langsung', '1' => 'Tidak Berhubungan Langsung', '2' => 'Barang Konsumsi',
            '3' => 'Barang Hasil Olahan', '4' => 'Barang Lainnya'],
        'kondisiBarang' => ['1' => 'Tidak Rusak', '2' => 'Rusak'],
    ];

    // Pemeriksaan saat KIRIM terhadap skema resmi docs/bc_schema/bc25.json (bukan saat simpan). Kolom teks di bawah
    // ini tidak boleh kosong walau skema hanya menuntut tipe string (keputusan user); yang boleh kosong: NIB, NIPER,
    // merk kemasan, nama / nomor pengangkut, merk / tipe / ukuran bahan baku.
    private const WAJIB_ISI = [
        'kodeKantor', 'kodeTujuanPengiriman', 'kodeJenisTpb', 'kodeCaraBayar', 'kodeValuta', 'kotaTtd', 'namaTtd', 'jabatanTtd',
        'barang.kodeBarang', 'barang.uraian', 'barang.posTarif', 'barang.kodeSatuanBarang', 'barang.kodeJenisKemasan',
        'barang.kodeKategoriBarang', 'barang.kodeGunaBarang', 'barang.kodeKondisiBarang', 'barang.kodeDokAsal',
        'barang.bahanBaku.kodeBarang', 'barang.bahanBaku.posTarif', 'barang.bahanBaku.uraianBarang', 'barang.bahanBaku.kodeSatuanBarang',
        'barang.bahanBaku.kodeDokAsal', 'barang.bahanBaku.kodeKantor', 'barang.bahanBaku.nomorDaftarDokAsal', 'barang.bahanBaku.nomorAjuDokAsal',
        'entitas.namaEntitas', 'entitas.alamatEntitas', 'entitas.nomorIdentitas',
        'dokumen.kodeDokumen', 'dokumen.nomorDokumen',
    ];

    // Salinan data master ENTITAS di dokumen (Q-f): [key camelCase => akhiran kolom]. Kolomnya
    // <akhiran><n>, mis. KODEJENISAPIENTITAS1. Diisi server saat simpan dari ENTITAS.id yang dipilih.
    private const SALINAN_ENTITAS = [
        'kodeJenisApi' => 'KODEJENISAPIENTITAS',
        'kodeJenisIdentitas' => 'KODEJENISIDENTITASENTITAS',
        'kodeStatus' => 'KODESTATUSENTITAS',
        'nibEntitas' => 'NIBENTITAS',
        'kodeNegara' => 'KODENEGARAENTITAS',
    ];

    public function __construct(private Bc25PayloadBuilder $builder, private CeisaService $ceisa, private CeisaSchemaValidator $skema) {}

    public function index(Request $req)
    {
        $kodemenu = '';
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu, $req->path());
        if (! $akses || ! $akses->HASACCESS) {
            return redirect('/home');
        }

        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

        return view('documentbc.documentbc25', [
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
                'kategori' => $this->daftarKode('kategori_barang'),
                'satuan' => $this->daftarKode('satuan'),
                'kemasan' => $this->daftarKode('kemasan'),
                'jenisDokumen' => $this->daftarKode('jenis_dokumen'),
            ],
        ]);
    }

    // Daftar transaksi: semua BC 2.5, tanpa filter periode (plan §4.1). Tanggal = arr_date (Tanggal Pengeluaran).
    public function loadAll()
    {
        return DB::connection('SML')->select('
            SELECT ID AS id, NOMORAJU AS nomorAju, ARRDATE AS arrDate, NAMAENTITAS3 AS namaEntitas3,
                   ISNULL(SENT, 0) AS sent, TGLKIRIM AS tglKirim
            FROM '.$this->tabel('bc25').'
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

        return DB::connection('SML')->transaction(function () use ($header, $children) {
            $kolom = $this->keKolom(self::HEADER, $header) + $this->salinanEntitas($header) + [
                'USERBUAT' => \Auth::user()->username,
            ];
            $id = (int) $this->sisipkan($this->tabel('bc25'), $kolom, true);
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

        return DB::connection('SML')->transaction(function () use ($id, $header, $children) {
            if ($tolak = $this->cekBolehUbah($id)) {
                return $tolak;
            }

            // Header ditimpa (SENT tidak diubah), anak diganti seluruhnya.
            $kolom = $this->keKolom(self::HEADER, $header) + $this->salinanEntitas($header);
            $set = implode(', ', array_map(fn ($c) => "{$c} = ?", array_keys($kolom)));
            DB::connection('SML')->update(
                'UPDATE '.$this->tabel('bc25')." SET {$set}, USERUBAH = ?, TGLUBAH = GETDATE() WHERE ID = ?",
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
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel('bc25').' WHERE ID = ?', [$id]);

            return response()->json(['message' => 'Data telah dihapus.']);
        });
    }

    // Kirim ke CEISA (docs/documentbc25_plan.md §8; sama dengan BC 2.6.1).
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
            'UPDATE '.$this->tabel('bc25').' SET SENT = 1, IDHEADER = ?, TGLKIRIM = GETDATE(), USERKIRIM = ? WHERE ID = ?',
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
        $akses = app('App\Http\Controllers\GlobalController')->getAkses('', 'documentbc25');
        if (! $akses || ! $akses->HASACCESS || ! $akses->$hak) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        return null;
    }

    // Skema resmi + aturan BC 2.5 di luar skema: minimal 1 barang, dokumen pertama harus Invoice (380) dan wajib ada,
    // valuta harus sudah berupa kode ISO 3 huruf (config beacukai.valuta_iso), idPengguna (username CEISA dari
    // Login BC) harus ada.
    private function masalahKirim(array $payload): array
    {
        $masalah = $this->skema->masalah('bc25', $payload, [
            'bagian' => [
                'barang' => 'Barang', 'barangTarif' => ['Tarif BM', 'Tarif PPN', 'Tarif PPH'], 'bahanBaku' => 'Bahan Baku',
                'dokumen' => 'Dokumen', 'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Penerima Barang'],
                'kemasan' => 'Kemasan', 'kontainer' => 'Peti Kemas', 'pengangkut' => 'Pengangkut',
            ],
            'wajibIsi' => self::WAJIB_ISI,
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer', 'bahanBaku'],
        ]);

        if (! $payload['barang']) {
            $masalah[] = 'Barang: minimal 1 barang.';
        }
        if (! $payload['dokumen']) {
            $masalah[] = 'Dokumen: wajib ada, dokumen pertama adalah Invoice (380).';
        }
        if ($payload['kodeValuta'] !== '' && ! preg_match('/^[A-Z]{3}$/', $payload['kodeValuta'])) {
            $masalah[] = "Valuta ({$payload['kodeValuta']}): belum punya kode ISO 3 huruf; tambahkan padanannya di config/beacukai.php (valuta_iso) atau ganti valuta.";
        }
        if (trim($payload['idPengguna']) === '') {
            $masalah[] = 'ID Pengguna (idPengguna): belum ada. Klik Login BC sekali supaya username CEISA tersimpan.';
        }

        return $masalah;
    }

    // Validasi simpan: Nomor AJU wajib (Q-l) dan Berat Kotor harus > 0 (plan §5.2; 422, bukan 200 - Q-n).
    private function validasiHeader(array $header)
    {
        $errors = [];
        if ($header['nomorAju'] === '') {
            $errors['nomorAju'] = 'Nomor AJU harus diisi.';
        }
        if ((float) $header['bruto'] <= 0) {
            $errors['bruto'] = 'Berat Kotor harus lebih besar dari 0';
        }

        return $errors ? response()->json(['message' => reset($errors), 'errors' => $errors], 422) : null;
    }

    // Dokumen yang sudah terkirim ke CEISA dikunci dari Edit/Hapus (Q-i). Dipanggil di dalam
    // transaksi; UPDLOCK menahan baris sampai commit supaya tidak bentrok dengan pengiriman.
    private function cekBolehUbah(int $id)
    {
        $doc = DB::connection('SML')->selectOne(
            'SELECT ID, ISNULL(SENT, 0) AS SENT FROM '.$this->tabel('bc25').' WITH (UPDLOCK, HOLDLOCK) WHERE ID = ?', [$id]
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
                    SELECT e.KODEJENISAPI, e.KODEJENISIDENTITAS, e.KODESTATUS, e.NIBENTITAS, e.NIPERENTITAS, e.NOMORIJINIDENTITAS,
                           CONVERT(varchar(10), e.TANGGALIJINIDENTITAS, 23) AS TANGGALIJIN, ng.KODENEGARA
                    FROM '.$this->tabel('entitas').' e
                    LEFT JOIN '.$this->tabel('negara').' ng ON ng.id = e.IDNEGARA
                    WHERE e.id = ?', [$idEntitas]);
            }
            $isi = fn ($v) => $v === null || trim((string) $v) === '' ? null : trim((string) $v);
            $kolom["KODEJENISAPIENTITAS{$n}"] = $e ? $isi($e->KODEJENISAPI) : null;
            $kolom["KODEJENISIDENTITASENTITAS{$n}"] = $e ? $isi($e->KODEJENISIDENTITAS) : null;
            $kolom["KODESTATUSENTITAS{$n}"] = $e ? $isi($e->KODESTATUS) : null;
            $kolom["NIBENTITAS{$n}"] = $e ? $isi($e->NIBENTITAS) : null;
            $kolom["KODENEGARAENTITAS{$n}"] = $e ? $isi($e->KODENEGARA) : null;

            // Penerima Barang: NIPER (wajib di skema resmi) dan izin tidak diketik di form: ikut disalin dari master.
            if ($n === 3) {
                $kolom['NIPERENTITAS3'] = $e ? $isi($e->NIPERENTITAS) : null;
                $kolom['NOMORIJINENTITAS3'] = $e ? $isi($e->NOMORIJINIDENTITAS) : null;
                $kolom['TANGGALIJINENTITAS3'] = $e ? $this->tanggal($e->TANGGALIJIN) : null;
            }
        }

        return $kolom;
    }

    // Header + koleksi anak dalam bentuk camelCase bertipe PHP (desimal float, tanggal 'Y-m-d').
    private function muatDokumen(int $id): ?array
    {
        $row = DB::connection('SML')->selectOne('SELECT * FROM '.$this->tabel('bc25').' WHERE ID = ?', [$id]);
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
            $rows = DB::connection('SML')->select('SELECT * FROM '.$this->tabel($kunciTabel)." WHERE IDBC25 = ? ORDER BY {$order}", [$id]);
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
        $entitas[0]['nomorIjinEntitas'] = $h['nomorIjinEntitas1'] ?? null;
        $entitas[0]['tanggalIjinEntitas'] = $h['tanggalIjinEntitas1'] ?? null;
        $entitas[2]['niperEntitas'] = $row['NIPERENTITAS3'] ?? null;

        // idPengguna = username CEISA dari Login BC terakhir user ini (bukan username ERP).
        $header = $doc['header'] + ['idPengguna' => (string) $this->ceisa->username(\Auth::user()->username)];

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
                $this->sisipkan($this->tabel($kunciTabel), ['IDBC25' => $id] + $this->keKolom($peta, $row));
            }
        }
    }

    private function hapusAnak(int $id): void
    {
        foreach (self::CHILDREN as [$kunciTabel]) {
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel($kunciTabel).' WHERE IDBC25 = ?', [$id]);
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
