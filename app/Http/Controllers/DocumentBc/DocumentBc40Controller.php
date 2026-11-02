<?php

namespace App\Http\Controllers\DocumentBc;

use App\Http\Controllers\Controller;
use App\Services\BeaCukai\Bc40PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use App\Services\BeaCukai\CeisaService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Dokumen BC 4.0 - pemasukan barang dari dalam negeri ke kawasan berikat (spesifikasi:
// docs/documentbc40_plan.md). Sama dengan BC 2.3 (DocumentBc23Controller) tetapi SUMBER = PO pemasok
// DALAM negeri (DBCUSTSUPP.NEGARA = 'INDONESIA'): PO yang tidak batal, sudah IsOtorisasi1, punya
// baris barang (bukan jasa), tanggalnya dalam rentang yang dipilih, dan belum punya baris di
// DOCUMENTBC40 (NOBUKTI) tampil di Outstanding. DBPO / DBPODET tidak diubah. Satu PO = satu BC 4.0.
// Header + 2 koleksi (barang, dokumen) disimpan sekaligus dari satu JSON; dikirim ke CEISA lewat
// Bc40PayloadBuilder + CeisaService. Nama tabel di config/beacukai.php, kolom di
// docs/update-db-DBSMLNEW/beacukai/12_tabel_documentbc40.sql.
//
// JS/payload memakai key camelCase (nama logis di spesifikasi), tabel memakai kolom UPPERCASE.
// Peta di bawah = satu-satunya tempat pemetaan keduanya. Tipe:
//   str   teks NOT NULL ('' bila kosong)      strn  teks nullable ('' → null)
//   dec   desimal NOT NULL (0 bila kosong)    int   bulat nullable
//   intz  bulat NOT NULL (0 bila kosong)      date  'Y-m-d' atau null
//   seri  posisi baris 1..n (diisi server)
// Elemen ketiga (opsional) = nilai bawaan bila kosong.
class DocumentBc40Controller extends Controller
{
    private const HEADER = [
        'nomorAju' => ['NOMORAJU', 'strn'],
        'kodeKantor' => ['KODEKANTOR', 'strn'],
        'kodeJenisTpb' => ['KODEJENISTPB', 'strn'],
        'kodeTujuanPengirim' => ['KODETUJUANPENGIRIM', 'str', '1'],
        'nomorDokumenPabean' => ['NOMORDOKUMENPABEAN', 'strn'],
        'tanggalAju' => ['TANGGALAJU', 'date'],
        'hargaPenyerahan' => ['HARGAPENYERAHAN', 'dec'],
        'nilaiJasa' => ['NILAIJASA', 'dec'],
        'uangMuka' => ['UANGMUKA', 'dec'],
        'bruto' => ['BRUTO', 'dec'],
        'netto' => ['NETTO', 'dec'],
        'volume' => ['VOLUME', 'dec'],
        'nomorIdentitasEntitas1' => ['NOMORIDENTITASENTITAS1', 'strn'],
        'namaEntitas1' => ['NAMAENTITAS1', 'str'],
        'alamatEntitas1' => ['ALAMATENTITAS1', 'str'],
        'nomorIjinEntitas1' => ['NOMORIJINENTITAS1', 'strn'],
        'nomorIdentitasEntitas2' => ['NOMORIDENTITASENTITAS2', 'strn'],
        'namaEntitas2' => ['NAMAENTITAS2', 'str'],
        'alamatEntitas2' => ['ALAMATENTITAS2', 'str'],
        'nomorIdentitasEntitas3' => ['NOMORIDENTITASENTITAS3', 'strn'],
        'namaEntitas3' => ['NAMAENTITAS3', 'str'],
        'alamatEntitas3' => ['ALAMATENTITAS3', 'str'],
        'selectEntitas1' => ['SELECTENTITAS1', 'int'],
        'selectEntitas2' => ['SELECTENTITAS2', 'int'],
        'selectEntitas3' => ['SELECTENTITAS3', 'int'],
        'namaPengangkut' => ['NAMAPENGANGKUT', 'str'],
        'nomorPengangkut' => ['NOMORPENGANGKUT', 'str'],
        'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'str'],
        'jumlahKemasan' => ['JUMLAHKEMASAN', 'intz'],
        'merkKemasan' => ['MERKKEMASAN', 'str'],
        'kotaTtd' => ['KOTATTD', 'strn'],
        'tanggalTtd' => ['TANGGALTTD', 'date'],
        'namaTtd' => ['NAMATTD', 'strn'],
        'jabatanTtd' => ['JABATANTTD', 'strn'],
    ];

    // Koleksi anak: [key config tabel, kolom urutan baca, peta kolom].
    private const CHILDREN = [
        'barang' => ['bc40_barang', 'SERIBARANG', [
            'seriBarang' => ['SERIBARANG', 'seri'],
            'posTarif' => ['POSTARIF', 'str'],
            'kodeBarang' => ['KODEBARANG', 'str'],
            'uraian' => ['URAIAN', 'str'],
            'merk' => ['MERK', 'str'],
            'tipe' => ['TIPE', 'str'],
            'ukuran' => ['UKURAN', 'str'],
            'spesifikasiLain' => ['SPESIFIKASILAIN', 'str'],
            'kodeSatuanBarang' => ['KODESATUANBARANG', 'str'],
            'jumlahSatuan' => ['JUMLAHSATUAN', 'dec'],
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'str'],
            'jumlahKemasan' => ['JUMLAHKEMASAN', 'intz'],
            'netto' => ['NETTO', 'dec'],
            'volume' => ['VOLUME', 'dec'],
            'hargaPenyerahan' => ['HARGAPENYERAHAN', 'dec'],
        ]],
        'dokumen' => ['bc40_dokumen', 'SERIDOKUMEN', [
            'seriDokumen' => ['SERIDOKUMEN', 'seri'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str'],
            'nomorDokumen' => ['NOMORDOKUMEN', 'strn'],
            'tanggalDokumen' => ['TANGGALDOKUMEN', 'date'],
        ]],
    ];

    // Daftar kode tetap (docs/documentbc40_plan.md Lampiran A).
    private const KODE = [
        'tujuanPengirim' => ['1' => 'Penyerahan BKP', '2' => 'Penyerahan JKP', '3' => 'Retur', '4' => 'Non Penyerahan', '5' => 'Lainnya'],
    ];

    // Waktu tunggu kirim BC 4.0 (BC 2.6.1 memakai 30) - docs/documentbc40_plan.md §8.
    private const TIMEOUT_KIRIM = 60;

    // Pemeriksaan saat KIRIM terhadap skema resmi docs/bc_schema/bc40.json (bukan saat simpan). Kolom teks di bawah
    // ini tidak boleh kosong walau skema hanya menuntut tipe string (keputusan user); yang boleh kosong: merk /
    // tipe / ukuran / spesifikasi lain barang dan merk kemasan.
    private const WAJIB_ISI = [
        'kodeKantor', 'kodeJenisTpb', 'kodeTujuanPengiriman', 'kotaTtd', 'namaTtd', 'jabatanTtd',
        'pengangkut.namaPengangkut', 'pengangkut.nomorPengangkut', 'kemasan.kodeJenisKemasan',
        'barang.kodeBarang', 'barang.uraian', 'barang.posTarif', 'barang.kodeSatuanBarang', 'barang.kodeJenisKemasan',
        'entitas.namaEntitas', 'entitas.alamatEntitas', 'entitas.nomorIdentitas', 'entitas.nibEntitas', 'entitas.nomorIjinEntitas',
        'dokumen.kodeDokumen', 'dokumen.nomorDokumen',
    ];

    public function __construct(private Bc40PayloadBuilder $builder, private CeisaService $ceisa, private CeisaSchemaValidator $skema) {}

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

        return view('documentbc.documentbc40', [
            'periode' => $periode,
            'menul0' => $menul0,
            'date1' => $date1,
            'date2' => $date2,
            'akses' => $akses,
            'kodeKantor' => config('beacukai.kode_kantor'),
            'kode' => self::KODE,
            'entitas' => $this->daftarEntitas(),
            'lookup' => [
                'satuan' => $this->daftarKode('satuan'),
                'kemasan' => $this->daftarKode('kemasan'),
                'jenisDokumen' => $this->daftarKode('jenis_dokumen'),
            ],
        ]);
    }

    // Outstanding: semua PO pemasok dalam negeri yang belum punya BC 4.0 (tanpa filter periode, sama seperti BC 2.3).
    // DBPO / DBPODET hanya dibaca. PO tanpa baris barang (hanya jasa) tidak ditampilkan.
    public function loadOutstanding()
    {
        return DB::connection('SML')->select('
            SELECT a.NOBUKTI AS nobukti, a.TANGGAL AS tanggal, a.KODESUPP AS kodeSupp, b.NAMACUSTSUPP AS namaSupp,
                   a.KODEVLS AS kodeValuta,
                   (SELECT COUNT(*) FROM DBPODET d WHERE d.NOBUKTI = a.NOBUKTI AND ISNULL(d.Isbatal, 0) = 0 AND ISNULL(d.Isjasa, 0) = 0) AS jumlahBaris
            FROM DBPO a
            JOIN DBCUSTSUPP b ON b.KODECUSTSUPP = a.KODESUPP
            WHERE b.NEGARA = \'INDONESIA\'
              AND ISNULL(a.IsBatal, 0) = 0
              AND a.IsOtorisasi1 = 1
              AND EXISTS (SELECT 1 FROM DBPODET d WHERE d.NOBUKTI = a.NOBUKTI AND ISNULL(d.Isbatal, 0) = 0 AND ISNULL(d.Isjasa, 0) = 0)
              AND NOT EXISTS (SELECT 1 FROM '.$this->tabel('bc40').' x WHERE x.NOBUKTI = a.NOBUKTI)
            ORDER BY a.TANGGAL DESC, a.NOBUKTI DESC');
    }

    // Daftar transaksi: rentang tanggal pada TANGGALAJU (dokumen tanpa tanggal AJU memakai tanggal buat).
    public function loadAll(Request $req)
    {
        $date1 = $this->tanggal($req->date1) ?? date('Y-m-01');
        $date2 = $this->tanggal($req->date2) ?? date('Y-m-t');

        return DB::connection('SML')->select('
            SELECT ID AS id, NOBUKTI AS revDoc, NOMORAJU AS nomorAju, TANGGALAJU AS tanggalAju,
                   NAMAENTITAS3 AS namaEntitas3, ISNULL(SENT, 0) AS sent, TGLKIRIM AS tglKirim
            FROM '.$this->tabel('bc40').'
            WHERE ISNULL(TANGGALAJU, CAST(TGLBUAT AS date)) BETWEEN :date1 AND :date2
            ORDER BY ID DESC', ['date1' => $date1, 'date2' => $date2]);
    }

    // Isi PO untuk Detail (baca saja) dan untuk mengisi awal form Tambah: header PO + baris barang DBPODET.
    public function spSumber(Request $req)
    {
        $po = DB::connection('SML')->selectOne('
            SELECT a.NOBUKTI, a.TANGGAL, a.KODESUPP, b.NAMACUSTSUPP, b.NEGARA, a.KODEVLS, a.KETERANGAN
            FROM DBPO a
            LEFT JOIN DBCUSTSUPP b ON b.KODECUSTSUPP = a.KODESUPP
            WHERE a.NOBUKTI = ?', [(string) $req->nobukti]);
        if (! $po) {
            return response()->json(['message' => 'PO tidak ditemukan.'], 404);
        }

        $dipakai = (bool) DB::connection('SML')->selectOne(
            'SELECT 1 AS ada FROM '.$this->tabel('bc40').' WHERE NOBUKTI = ?', [$po->NOBUKTI]
        );

        $rows = DB::connection('SML')->select('
            SELECT c.URUT, c.KODEBRG, c.NamaBrg, g.NAMABRG, c.QNT, c.QntBatal, c.SATUAN, c.SUBTOTAL, c.SUBTOTALRp, g.Berat
            FROM DBPODET c
            LEFT JOIN DBBARANG g ON g.KODEBRG = c.KODEBRG
            WHERE c.NOBUKTI = ? AND ISNULL(c.Isbatal, 0) = 0 AND ISNULL(c.Isjasa, 0) = 0
            ORDER BY c.URUT', [$po->NOBUKTI]);

        // Isian awal barang: baris jasa dan baris dengan sisa qty <= 0 dilewati. Harga Penyerahan
        // (Rupiah) = SUBTOTAL (sebelum PPN); PO bermata uang selain IDR memakai SUBTOTALRp supaya tetap
        // Rupiah. Diperkecil sebanding bila sebagian qty dibatalkan (QntBatal). Netto = DBBARANG.Berat x
        // qty (0 bila berat belum diisi). Selebihnya diisi user.
        $rupiah = strtoupper(trim((string) $po->KODEVLS)) === 'IDR';
        $barang = [];
        foreach ($rows as $r) {
            $qnt = (float) $r->QNT;
            $qty = $qnt - (float) $r->QntBatal;
            if ($qty <= 0) {
                continue;
            }
            $subtotal = (float) ($rupiah ? $r->SUBTOTAL : $r->SUBTOTALRp);
            $barang[] = [
                'kodeBarang' => (string) $r->KODEBRG,
                'uraian' => mb_substr(trim((string) $r->NamaBrg) !== '' ? trim((string) $r->NamaBrg) : trim((string) $r->NAMABRG), 0, 100),
                'kodeSatuanBarang' => trim((string) $r->SATUAN),
                'jumlahSatuan' => $qty,
                'hargaPenyerahan' => round((float) $r->QntBatal > 0 && $qnt > 0 ? $subtotal * $qty / $qnt : $subtotal, 4),
                'netto' => round((float) $r->Berat * $qty, 4),
                'volume' => 0,
            ];
        }

        // Pemasok PO = Pengirim Barang (Entitas 3): dipilih otomatis bila ada entitas bernama sama.
        $entitas3 = DB::connection('SML')->selectOne(
            'SELECT TOP 1 id FROM '.$this->tabel('entitas').' WHERE UPPER(LTRIM(RTRIM(NAMAENTITAS))) = UPPER(LTRIM(RTRIM(?))) ORDER BY id',
            [(string) $po->NAMACUSTSUPP]
        );

        return [
            'po' => [
                'nobukti' => $po->NOBUKTI,
                'tanggal' => $po->TANGGAL ? substr((string) $po->TANGGAL, 0, 10) : '',
                'kodeSupp' => $po->KODESUPP,
                'namaSupp' => $po->NAMACUSTSUPP,
                'negara' => $po->NEGARA,
                'kodeValuta' => trim((string) $po->KODEVLS),
                'keterangan' => trim((string) $po->KETERANGAN),
                'dipakai' => $dipakai,
            ],
            'header' => [
                'selectEntitas3' => $entitas3 ? (int) $entitas3->id : null,
            ],
            'barang' => $barang,
        ];
    }

    // Pemilih HS (POSTARIFHS): 50 kode pertama yang cocok. Hanya untuk mengisi pos tarif barang
    // (tarif tidak dipakai di BC 4.0 - persen tidak dikirim).
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

        // Tanggal kosong dikirim '' supaya bisa langsung diisikan ke <input type="date"> (Q-r: aman null).
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
        $nobukti = trim((string) ($data['nobukti'] ?? ''));
        if (! $data || $nobukti === '') {
            return response()->json(['message' => 'Data tidak valid.'], 422);
        }

        $header = $this->normalisasi(self::HEADER, $data['header'] ?? []);
        $children = $this->normalisasiAnak($data);

        return DB::connection('SML')->transaction(function () use ($nobukti, $header, $children) {
            // Satu PO satu BC 4.0; UPDLOCK menahan sampai commit supaya dua user tidak memakai PO yang sama.
            $sudah = DB::connection('SML')->selectOne(
                'SELECT ID FROM '.$this->tabel('bc40').' WITH (UPDLOCK, HOLDLOCK) WHERE NOBUKTI = ?', [$nobukti]
            );
            if ($sudah) {
                return response()->json(['message' => "PO {$nobukti} sudah dibuatkan BC 4.0."], 409);
            }
            if (! $this->poBolehDipakai($nobukti)) {
                return response()->json(['message' => "PO {$nobukti} tidak memenuhi syarat BC 4.0 (pemasok dalam negeri, tidak batal, sudah otorisasi)."], 422);
            }

            $kolom = $this->keKolom(self::HEADER, $header) + [
                'NOBUKTI' => $nobukti,
                'BCTYPE' => '40',
                'USERBUAT' => \Auth::user()->username,
            ];
            $id = (int) $this->sisipkan($this->tabel('bc40'), $kolom, true);
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
        $children = $this->normalisasiAnak($data);

        return DB::connection('SML')->transaction(function () use ($id, $header, $children) {
            if ($tolak = $this->cekBolehUbah($id)) {
                return $tolak;
            }

            // Header ditimpa (NOBUKTI, BCTYPE & SENT tidak diubah), anak diganti seluruhnya.
            $kolom = $this->keKolom(self::HEADER, $header);
            $set = implode(', ', array_map(fn ($c) => "{$c} = ?", array_keys($kolom)));
            DB::connection('SML')->update(
                'UPDATE '.$this->tabel('bc40')." SET {$set}, USERUBAH = ?, TGLUBAH = GETDATE() WHERE ID = ?",
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
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel('bc40').' WHERE ID = ?', [$id]);

            // PO kembali ke Outstanding otomatis (barisnya di DOCUMENTBC40 sudah tidak ada).
            return response()->json(['message' => 'Data telah dihapus.']);
        });
    }

    // Kirim ke CEISA (docs/documentbc40_plan.md §8).
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
            $res = $this->ceisa->send($token, $this->builder->encode($payload), self::TIMEOUT_KIRIM);
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

        // Q-a: Villiger menganggap semua HTTP 2xx berhasil; di sini harus status OK.
        if (($json['status'] ?? null) !== 'OK') {
            return response()->json([
                'message' => 'Pengiriman gagal.', 'details' => CeisaService::details($raw), 'response' => $json,
            ], 400);
        }

        $idHeader = CeisaService::cariNilai($json, ['idHeader']);
        DB::connection('SML')->update(
            'UPDATE '.$this->tabel('bc40').' SET SENT = 1, IDHEADER = ?, TGLKIRIM = GETDATE(), USERKIRIM = ? WHERE ID = ?',
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
        $akses = app('App\Http\Controllers\GlobalController')->getAkses('', 'documentbc40');
        if (! $akses || ! $akses->HASACCESS || ! $akses->$hak) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        return null;
    }

    // Skema resmi + aturan BC 4.0 di luar skema: minimal 1 barang, kemasan wajib (jenis terisi dan jumlah > 0).
    // Dokumen pelengkap tidak diwajibkan, tetapi baris yang ada harus lengkap (jenis, nomor, tanggal).
    private function masalahKirim(array $payload): array
    {
        $masalah = $this->skema->masalah('bc40', $payload, [
            'bagian' => [
                'barang' => 'Barang', 'barangTarif' => ['Tarif PPN'], 'dokumen' => 'Dokumen', 'kemasan' => 'Kemasan',
                'entitas' => ['Pengusaha TPB', 'Pemilik Barang', 'Pengirim Barang'], 'pengangkut' => 'Pengangkut', 'pungutan' => 'Pungutan',
            ],
            'wajibIsi' => self::WAJIB_ISI,
            'sisa' => ['barang', 'dokumen'],
        ]);

        if (! $payload['barang']) {
            $masalah[] = 'Barang: minimal 1 barang.';
        }
        if (! $payload['kemasan']) {
            $masalah[] = 'Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).';
        } elseif ($payload['kemasan'][0]['jumlahKemasan'] <= 0) {
            $masalah[] = 'Kemasan > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0';
        }

        return $masalah;
    }

    // Syarat sumber (tanpa cek "sudah dipakai" dan rentang tanggal): pemasok dalam negeri, PO tidak
    // batal, sudah IsOtorisasi1.
    private function poBolehDipakai(string $nobukti): bool
    {
        return (bool) DB::connection('SML')->selectOne('
            SELECT 1 AS ada
            FROM DBPO a
            JOIN DBCUSTSUPP b ON b.KODECUSTSUPP = a.KODESUPP
            WHERE a.NOBUKTI = ? AND b.NEGARA = \'INDONESIA\' AND ISNULL(a.IsBatal, 0) = 0 AND a.IsOtorisasi1 = 1', [$nobukti]);
    }

    // Dokumen yang sudah terkirim ke CEISA dikunci dari Edit/Hapus (Q-f). Dipanggil di dalam
    // transaksi; UPDLOCK menahan baris sampai commit supaya tidak bentrok dengan pengiriman.
    private function cekBolehUbah(int $id)
    {
        $doc = DB::connection('SML')->selectOne(
            'SELECT ID, ISNULL(SENT, 0) AS SENT FROM '.$this->tabel('bc40').' WITH (UPDLOCK, HOLDLOCK) WHERE ID = ?', [$id]
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

    private function daftarEntitas(): array
    {
        return DB::connection('SML')->select('
            SELECT id, NAMAENTITAS AS namaEntitas, ALAMATENTITAS AS alamatEntitas, NOMORIDENTITAS AS nomorIdentitas,
                   NOMORIJINIDENTITAS AS nomorIjinEntitas
            FROM '.$this->tabel('entitas').'
            ORDER BY NAMAENTITAS');
    }

    // Header + koleksi anak dalam bentuk camelCase bertipe PHP (desimal float, tanggal 'Y-m-d').
    private function muatDokumen(int $id): ?array
    {
        $row = DB::connection('SML')->selectOne('SELECT * FROM '.$this->tabel('bc40').' WHERE ID = ?', [$id]);
        if (! $row) {
            return null;
        }
        $row = (array) $row;

        $header = ['id' => (int) $row['ID'], 'nobukti' => (string) $row['NOBUKTI'], 'sent' => (int) ($row['SENT'] ?? 0), 'idHeader' => $row['IDHEADER'] ?? null]
            + $this->dariBaris(self::HEADER, $row);

        $children = [];
        foreach (self::CHILDREN as $nama => [$kunciTabel, $urut, $peta]) {
            $rows = DB::connection('SML')->select('SELECT * FROM '.$this->tabel($kunciTabel)." WHERE IDBC40 = ? ORDER BY {$urut}, ID", [$id]);
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

        // Data entitas yang dikirim diambil dari master (bukan salinan di dokumen) - Q-e.
        $entitas = [];
        foreach ([1, 2, 3] as $i) {
            $idEntitas = $doc['header']["selectEntitas{$i}"];
            $e = $idEntitas ? DB::connection('SML')->selectOne('
                SELECT e.NAMAENTITAS, e.ALAMATENTITAS, e.NOMORIDENTITAS, e.NIBENTITAS, e.NOMORIJINIDENTITAS,
                       CONVERT(varchar(10), e.TANGGALIJINIDENTITAS, 23) AS TANGGALIJIN, e.KODEJENISAPI,
                       e.KODEJENISIDENTITAS, e.KODESTATUS
                FROM '.$this->tabel('entitas').' e
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
            ] : null;
        }

        // Q-c: idPengguna = username ERP yang login (bukan akun CEISA).
        $header = $doc['header'] + ['idPengguna' => \Auth::user()->username];

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
                'int' => $this->bulat($v),
                'intz' => $this->bulat($v) ?? 0,
                'date' => $this->tanggal($v),
            };
        }
        // Nomor AJU disimpan huruf besar (docs/documentbc40_plan.md §5.3).
        if (isset($out['nomorAju'])) {
            $out['nomorAju'] = mb_strtoupper($out['nomorAju']);
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
                $this->sisipkan($this->tabel($kunciTabel), ['IDBC40' => $id] + $this->keKolom($peta, $row));
            }
        }
    }

    private function hapusAnak(int $id): void
    {
        foreach (self::CHILDREN as [$kunciTabel]) {
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel($kunciTabel).' WHERE IDBC40 = ?', [$id]);
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
