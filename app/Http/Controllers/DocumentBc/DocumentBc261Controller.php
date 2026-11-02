<?php

namespace App\Http\Controllers\DocumentBc;

use App\Http\Controllers\Controller;
use App\Services\BeaCukai\Bc261PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use App\Services\BeaCukai\CeisaService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Dokumen BC 2.6.1 - barang keluar sementara dari kawasan berikat (spesifikasi:
// docs/documentbc261_plan.md). Header + 6 koleksi disimpan sekaligus dari satu JSON; dikirim
// ke CEISA lewat Bc261PayloadBuilder + CeisaService. Nama tabel di config/beacukai.php, kolom
// di docs/update-db-DBSMLNEW/beacukai/09_tabel_documentbc261.sql.
//
// JS/payload memakai key camelCase (nama logis di spesifikasi), tabel memakai kolom UPPERCASE.
// Peta di bawah = satu-satunya tempat pemetaan keduanya. Tipe:
//   str   teks NOT NULL ('' bila kosong)      strn  teks nullable ('' → null)
//   dec   desimal NOT NULL (0 bila kosong)    decn  desimal nullable
//   int   bulat nullable                      intz  bulat NOT NULL (0 bila kosong)
//   date  'Y-m-d' atau null                   seri  posisi baris 1..n (diisi server)
//   seris posisi baris sebagai teks ("1", "2", ...)
// Elemen ketiga (opsional) = nilai bawaan bila kosong.
class DocumentBc261Controller extends Controller
{
    private const HEADER = [
        'nomorAju' => ['NOMORAJU', 'str'],
        'kodeKantor' => ['KODEKANTOR', 'str'],
        'kodeTujuanPengiriman' => ['KODETUJUANPENGIRIMAN', 'str'],
        'kodeValuta' => ['KODEVALUTA', 'str'],
        'ndpbm' => ['NDPBM', 'dec'],
        'cif' => ['CIF', 'dec'],
        'bruto' => ['BRUTO', 'dec'],
        'netto' => ['NETTO', 'dec'],
        'kodeCaraAngkut' => ['KODECARAANGKUT', 'str'],
        'noRegBc' => ['NOREGBC', 'strn'],
        'tglBc' => ['TGLBC', 'date'],
        'tglKeluar' => ['TGLKELUAR', 'date'],
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
        'barang' => ['bc261_barang', 'SERIBARANG', [
            'seriBarang' => ['SERIBARANG', 'seri'],
            'posTarif' => ['POSTARIF', 'str'],
            'kodeBarang' => ['KODEBARANG', 'str'],
            'kodeAsalBarang' => ['KODEASALBARANG', 'strn'],
            'uraian' => ['URAIAN', 'str'],
            'merk' => ['MERK', 'strn'],
            'tipe' => ['TIPE', 'strn'],
            'ukuran' => ['UKURAN', 'strn'],
            'spesifikasiLain' => ['SPESIFIKASILAIN', 'strn'],
            'kodeSatuanBarang' => ['KODESATUANBARANG', 'str'],
            'jumlahSatuan' => ['JUMLAHSATUAN', 'dec'],
            'jumlahKemasan' => ['JUMLAHKEMASAN', 'dec'],
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'strn'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '261'],
            'netto' => ['NETTO', 'dec'],
            'cif' => ['CIF', 'dec'],
            'cifRupiah' => ['CIFRUPIAH', 'dec'],
            'ndpbm' => ['NDPBM', 'dec'],
            'kodeNegaraAsal' => ['KODENEGARAASAL', 'str'],
            'keteranganDokumen' => ['KETERANGANDOKUMEN', 'str'],
            'persenBM' => ['PERSENBM', 'dec'],
            'persenPPN' => ['PERSENPPN', 'dec'],
            'persenPPh' => ['PERSENPPH', 'dec'],
            'nilaiBM' => ['NILAIBM', 'dec'],
            'nilaiPPN' => ['NILAIPPN', 'dec'],
            'nilaiPPh' => ['NILAIPPH', 'dec'],
            'hargaPenyerahan' => ['HARGAPENYERAHAN', 'decn'],
            'hargaPerolehan' => ['HARGAPEROLEHAN', 'decn'],
            'hargaEkspor' => ['HARGAEKSPOR', 'decn'],
            'hargaSatuan' => ['HARGASATUAN', 'decn'],
            'diskon' => ['DISKON', 'decn'],
            'freight' => ['FREIGHT', 'decn'],
            'isiPerKemasan' => ['ISIPERKEMASAN', 'decn'],
            'kodePerhitungan' => ['KODEPERHITUNGAN', 'strn'],
        ]],
        // netto / ndpbm / seriIjin bahan baku selalu null (tidak ada isiannya) dan Nomor AJU Dok Asal tidak lagi diisi
        // (kolom NOMORAJUDOKASAL tetap ada di tabel, NULL) - tidak dipetakan.
        'bahanbaku' => ['bc261_bahanbaku', 'ID', [
            'seriBarang' => ['SERIBARANG', 'int'],
            'seriBahanBaku' => ['SERIBAHANBAKU', 'int'],
            'kodeAsalBahanBaku' => ['KODEASALBAHANBAKU', 'strn'],
            'posTarif' => ['POSTARIF', 'str'],
            'kodeBarang' => ['KODEBARANG', 'str'],
            'uraianBarang' => ['URAIANBARANG', 'str'],
            'merkBarang' => ['MERKBARANG', 'strn'],
            'tipeBarang' => ['TIPEBARANG', 'strn'],
            'ukuranBarang' => ['UKURANBARANG', 'strn'],
            'spesifikasiLainBarang' => ['SPESIFIKASILAINBARANG', 'strn'],
            'kodeSatuanBarang' => ['KODESATUANBARANG', 'str'],
            'jumlahSatuan' => ['JUMLAHSATUAN', 'dec'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '261'],
            'kodeKantorAsal' => ['KODEKANTORASAL', 'str'],
            'nomorDaftarDokAsal' => ['NOMORDAFTARDOKASAL', 'str'],
            'tanggalDaftarDokAsal' => ['TANGGALDAFTARDOKASAL', 'date'],
            'seriBarangDokAsal' => ['SERIBARANGDOKASAL', 'int'],
            'cif' => ['CIF', 'dec'],
            'cifRupiah' => ['CIFRUPIAH', 'dec'],
            'hargaPenyerahan' => ['HARGAPENYERAHAN', 'dec'],
            'hargaPerolehan' => ['HARGAPEROLEHAN', 'dec'],
            'nilaiJasa' => ['NILAIJASA', 'dec'],
        ]],
        'kemasan' => ['bc261_kemasan', 'ID', [
            'seriKemasan' => ['SERIKEMASAN', 'seris'],
            'jumlahKemasan' => ['JUMLAHKEMASAN', 'intz'],
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'strn'],
            'merkKemasan' => ['MERKKEMASAN', 'str'],
        ]],
        'kontainer' => ['bc261_kontainer', 'SERIKONTAINER', [
            'seriKontainer' => ['SERIKONTAINER', 'seri'],
            'nomorKontainer' => ['NOMORKONTAINER', 'strn'],
            'kodeUkuranKontainer' => ['KODEUKURANKONTAINER', 'str'],
            'kodeJenisKontainer' => ['KODEJENISKONTAINER', 'str'],
            'kodeTipeKontainer' => ['KODETIPEKONTAINER', 'str'],
        ]],
        'jaminan' => ['bc261_jaminan', 'ID', [
            'kodeJenisJaminan' => ['KODEJENISJAMINAN', 'strn'],
            'nomorJaminan' => ['NOMORJAMINAN', 'strn'],
            'tanggalJaminan' => ['TANGGALJAMINAN', 'date'],
            'nilaiJaminan' => ['NILAIJAMINAN', 'int'],
            'tanggalJatuhTempo' => ['TANGGALJATUHTEMPO', 'date'],
            'penjamin' => ['PENJAMIN', 'strn'],
            'nomorBpj' => ['NOMORBPJ', 'strn'],
            'tanggalBpj' => ['TANGGALBPJ', 'date'],
        ]],
        'dokumen' => ['bc261_dokumen', 'SERIDOKUMEN', [
            'seriDokumen' => ['SERIDOKUMEN', 'seri'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '261'],
            'nomorDokumen' => ['NOMORDOKUMEN', 'strn'],
            'tanggalDokumen' => ['TANGGALDOKUMEN', 'date'],
        ]],
    ];

    // Daftar kode tetap (docs/documentbc261_plan.md Lampiran A).
    private const KODE = [
        'tujuanPengiriman' => ['1' => 'Diperbaiki', '2' => 'Disubkontrakkan', '3' => 'Dipinjamkan', '4' => 'Barang Return', '5' => 'Lainnya'],
        // Skema resmi bc261.json hanya menerima 1..9 (Villiger juga menawarkan 10-12; ditolak CEISA).
        'caraAngkut' => ['1' => 'Laut', '2' => 'Kereta Api', '3' => 'Darat', '4' => 'Udara', '5' => 'Pos', '6' => 'Multimoda',
            '7' => 'Instalasi/Pipa', '8' => 'Perairan', '9' => 'Lainnya'],
        'jenisJaminan' => ['1' => 'Tunai', '2' => 'Bank Garansi', '3' => 'Customs Bond', '4' => 'Tertulis/CG', '5' => 'STTJ',
            '6' => 'Pusat', '7' => 'Indonesia EximBank', '8' => 'Perusahaan Penjaminan'],
        'jenisMuatan' => ['4' => 'Empty', '7' => 'LCL', '8' => 'FCL'],
        'dokumenAsal' => ['23' => 'BC 2.3', '27' => 'BC 2.7', '40' => 'BC 4.0'],
    ];

    // Pemeriksaan saat KIRIM terhadap skema resmi docs/bc_schema/bc261.json (bukan saat simpan). Kolom teks di bawah
    // ini tidak boleh kosong walau skema hanya menuntut tipe string (keputusan user). Dokumen dan jaminan boleh tidak
    // ada, tetapi baris yang ada harus lengkap; merk kemasan, merk / tipe / ukuran bahan baku boleh kosong.
    private const WAJIB_ISI = [
        'kodeKantor', 'kodeTujuanPengiriman', 'kodeValuta', 'kotaTtd', 'namaTtd', 'jabatanTtd',
        'barang.kodeBarang', 'barang.uraian', 'barang.posTarif', 'barang.kodeSatuanBarang', 'barang.kodeJenisKemasan',
        'barang.kodeAsalBarang', 'barang.kodeNegaraAsal',
        'barang.bahanBaku.kodeBarang', 'barang.bahanBaku.posTarif', 'barang.bahanBaku.uraianBarang',
        'barang.bahanBaku.kodeSatuanBarang', 'barang.bahanBaku.kodeDokAsal', 'barang.bahanBaku.kodeKantor',
        'barang.bahanBaku.nomorDaftarDokAsal',
        'entitas.namaEntitas', 'entitas.alamatEntitas', 'entitas.nomorIdentitas', 'entitas.nibEntitas', 'entitas.nomorIjinEntitas',
        'kemasan.kodeJenisKemasan', 'kontainer.nomorKontainer',
        'dokumen.kodeDokumen', 'dokumen.nomorDokumen',
        'jaminan.kodeJenisJaminan', 'jaminan.nomorJaminan', 'jaminan.nomorBpj', 'jaminan.penjamin',
    ];

    public function __construct(private Bc261PayloadBuilder $builder, private CeisaService $ceisa, private CeisaSchemaValidator $skema) {}

    public function index(Request $req)
    {
        $kodemenu = '';
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu, $req->path());
        if (! $akses || ! $akses->HASACCESS) {
            return redirect('/home');
        }

        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

        $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
        $date2 = date('Y-m-t', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

        return view('documentbc.documentbc261', [
            'periode' => $periode,
            'menul0' => $menul0,
            'date1' => $date1,
            'date2' => $date2,
            'akses' => $akses,
            'kodeKantor' => config('beacukai.kode_kantor'),
            'kode' => self::KODE,
            'entitas' => $this->daftarEntitas(),
            'valuta' => DB::connection('SML')->select('SELECT KODEVLS AS kode, NAMAVLS AS nama FROM dbVALAS ORDER BY KODEVLS'),
            'ukuranKontainer' => $this->daftarKontainer('ukuran_kontainer', 'KODEUKURANKONTAINER', 'NAMAUKURANKONTAINER'),
            'tipeKontainer' => $this->daftarKontainer('tipe_kontainer', 'KODETIPEKONTAINER', 'NAMATIPEKONTAINER'),
            'lookup' => [
                'negara' => DB::connection('SML')->select('SELECT KODENEGARA AS kode, NAMANEGARA AS nama FROM '.config('beacukai.tables.negara').' ORDER BY KODENEGARA'),
                'satuan' => $this->daftarKode('satuan'),
                'kemasan' => $this->daftarKode('kemasan'),
                'jenisDokumen' => $this->daftarKode('jenis_dokumen'),
            ],
        ]);
    }

    // Daftar transaksi: rentang tanggal pada TANGGALTTD (dokumen tanpa TTD memakai tanggal buat).
    public function loadAll(Request $req)
    {
        $date1 = $this->tanggal($req->date1) ?? date('Y-m-01');
        $date2 = $this->tanggal($req->date2) ?? date('Y-m-t');

        return DB::connection('SML')->select('
            SELECT ID AS id, NOMORAJU AS nomorAju, TANGGALTTD AS tanggalTtd,
                   NAMAENTITAS3 AS namaEntitas3, ISNULL(SENT, 0) AS sent, TGLKIRIM AS tglKirim
            FROM '.$this->tabel('bc261').'
            WHERE ISNULL(TANGGALTTD, CAST(TGLBUAT AS date)) BETWEEN :date1 AND :date2
            ORDER BY ID DESC', ['date1' => $date1, 'date2' => $date2]);
    }

    // Pemilih HS (POSTARIFHS): 50 kode pertama yang cocok. Tarif hanya informasi - tidak disalin
    // ke barang (persen BM/PPN/PPh tetap 0 seperti Villiger).
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
        $children = $this->normalisasiAnak($data);

        $id = DB::connection('SML')->transaction(function () use ($header, $children, $data) {
            $kolom = $this->keKolom(self::HEADER, $header) + [
                'SOURCEDOCUMENTID' => $this->bulat($data['source_document_id'] ?? null) ?: null,
                'USERBUAT' => \Auth::user()->username,
            ];
            $id = (int) $this->sisipkan($this->tabel('bc261'), $kolom, true);
            $this->sisipkanAnak($id, $children);
            // Outstanding (dokumen sumber + ready_sent) belum dibangun - lihat docs/documentbc261_plan.md §3.

            return $id;
        });

        return response()->json(['id' => $id, 'message' => 'Data telah ditambahkan.']);
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
        $children = $this->normalisasiAnak($data);

        return DB::connection('SML')->transaction(function () use ($id, $header, $children) {
            if ($tolak = $this->cekBolehUbah($id)) {
                return $tolak;
            }

            // Header ditimpa (SOURCEDOCUMENTID & SENT tidak diubah), anak diganti seluruhnya.
            $kolom = $this->keKolom(self::HEADER, $header);
            $set = implode(', ', array_map(fn ($c) => "{$c} = ?", array_keys($kolom)));
            DB::connection('SML')->update(
                'UPDATE '.$this->tabel('bc261')." SET {$set}, USERUBAH = ?, TGLUBAH = GETDATE() WHERE ID = ?",
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
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel('bc261').' WHERE ID = ?', [$id]);

            return response()->json(['message' => 'Data telah dihapus.']);
        });
    }

    // Kirim ke CEISA (docs/documentbc261_plan.md §8).
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
            'UPDATE '.$this->tabel('bc261').' SET SENT = 1, IDHEADER = ?, TGLKIRIM = GETDATE(), USERKIRIM = ? WHERE ID = ?',
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

    // Skema resmi + aturan BC 2.6.1 di luar skema: minimal 1 barang, minimal 1 kemasan dengan jumlah > 0, valuta harus
    // sudah berupa kode ISO 3 huruf (config beacukai.valuta_iso).
    private function masalahKirim(array $payload): array
    {
        $masalah = $this->skema->masalah('bc261', $payload, [
            'bagian' => [
                'barang' => 'Barang', 'bahanBaku' => 'Bahan Baku', 'dokumen' => 'Dokumen',
                'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Penerima Barang'], 'jaminan' => 'Jaminan',
                'kemasan' => 'Kemasan', 'kontainer' => 'Peti Kemas', 'pengangkut' => 'Pengangkut', 'pungutan' => 'Pungutan',
            ],
            'wajibIsi' => self::WAJIB_ISI,
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer', 'jaminan', 'bahanBaku'],
        ]);

        if (! $payload['barang']) {
            $masalah[] = 'Barang: minimal 1 barang.';
        }
        if (! $payload['kemasan']) {
            $masalah[] = 'Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).';
        }
        foreach ($payload['kemasan'] as $i => $kemasan) {
            if ($kemasan['jumlahKemasan'] <= 0) {
                $masalah[] = 'Kemasan #'.($i + 1).' > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0';
            }
        }
        if ($payload['kodeValuta'] !== '' && ! preg_match('/^[A-Z]{3}$/', $payload['kodeValuta'])) {
            $masalah[] = "Valuta ({$payload['kodeValuta']}): belum punya kode ISO 3 huruf; tambahkan padanannya di config/beacukai.php (valuta_iso) atau ganti valuta.";
        }

        return $masalah;
    }

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
        $akses = app('App\Http\Controllers\GlobalController')->getAkses('', 'documentbc261');
        if (! $akses || ! $akses->HASACCESS || ! $akses->$hak) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        return null;
    }

    // Dokumen yang sudah terkirim ke CEISA dikunci dari Edit/Hapus (Q-e). Dipanggil di dalam
    // transaksi; UPDLOCK menahan baris sampai commit supaya tidak bentrok dengan pengiriman.
    private function cekBolehUbah(int $id)
    {
        $doc = DB::connection('SML')->selectOne(
            'SELECT ID, ISNULL(SENT, 0) AS SENT FROM '.$this->tabel('bc261').' WITH (UPDLOCK, HOLDLOCK) WHERE ID = ?', [$id]
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

    // Header + koleksi anak dalam bentuk camelCase bertipe PHP (desimal float, tanggal 'Y-m-d').
    private function muatDokumen(int $id): ?array
    {
        $row = DB::connection('SML')->selectOne('SELECT * FROM '.$this->tabel('bc261').' WHERE ID = ?', [$id]);
        if (! $row) {
            return null;
        }
        $row = (array) $row;

        $header = ['id' => (int) $row['ID'], 'sent' => (int) ($row['SENT'] ?? 0), 'idHeader' => $row['IDHEADER'] ?? null]
            + $this->dariBaris(self::HEADER, $row);

        $children = [];
        foreach (self::CHILDREN as $nama => [$kunciTabel, $urut, $peta]) {
            $order = $urut === 'ID' ? 'ID' : "{$urut}, ID";
            $rows = DB::connection('SML')->select('SELECT * FROM '.$this->tabel($kunciTabel)." WHERE IDBC261 = ? ORDER BY {$order}", [$id]);
            $children[$nama] = array_map(fn ($r) => $this->dariBaris($peta, (array) $r), $rows);
        }

        return ['header' => $header, 'children' => $children];
    }

    private function susunPayload(int $id): ?array
    {
        $doc = $this->muatDokumen($id);
        if (! $doc) {
            return null;
        }

        // Data entitas yang dikirim diambil dari master (bukan salinan di dokumen) - Q-f.
        $entitas = [];
        foreach ([1, 2, 3] as $i) {
            $idEntitas = $doc['header']["selectEntitas{$i}"];
            $e = $idEntitas ? DB::connection('SML')->selectOne('
                SELECT e.NAMAENTITAS, e.ALAMATENTITAS, e.NOMORIDENTITAS, e.NIBENTITAS, e.NOMORIJINIDENTITAS,
                       CONVERT(varchar(10), e.TANGGALIJINIDENTITAS, 23) AS TANGGALIJIN, e.KODEJENISAPI,
                       e.KODEJENISIDENTITAS, e.KODESTATUS, n.KODENEGARA
                FROM '.$this->tabel('entitas').' e
                LEFT JOIN '.$this->tabel('negara').' n ON n.id = e.IDNEGARA
                WHERE e.id = ?', [$idEntitas]) : null;
            $entitas[] = $e ? [
                'namaEntitas' => $e->NAMAENTITAS,
                'alamatEntitas' => $e->ALAMATENTITAS,
                'nomorIdentitas' => $e->NOMORIDENTITAS,
                'nibEntitas' => $e->NIBENTITAS,
                'nomorIjinEntitas' => $e->NOMORIJINIDENTITAS,
                'tanggalIjinEntitas' => $e->TANGGALIJIN,
                'kodeJenisApi' => $e->KODEJENISAPI,
                'kodeJenisIdentitas' => $e->KODEJENISIDENTITAS,
                'kodeStatus' => $e->KODESTATUS,
                'kodeNegara' => $e->KODENEGARA,
            ] : null;
        }

        return $this->builder->build($doc['header'], $doc['children'], $entitas);
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
                'seris' => (string) $posisi,
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
        // barangnya (Q-k, bukan urutan global).
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
                'seris', 'str' => (string) ($v ?? ''),
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
                $this->sisipkan($this->tabel($kunciTabel), ['IDBC261' => $id] + $this->keKolom($peta, $row));
            }
        }
    }

    private function hapusAnak(int $id): void
    {
        foreach (self::CHILDREN as [$kunciTabel]) {
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel($kunciTabel).' WHERE IDBC261 = ?', [$id]);
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
