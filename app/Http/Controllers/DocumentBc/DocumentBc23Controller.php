<?php

namespace App\Http\Controllers\DocumentBc;

use App\Http\Controllers\Controller;
use App\Services\BeaCukai\Bc23PayloadBuilder;
use App\Services\BeaCukai\CeisaSchemaValidator;
use App\Services\BeaCukai\CeisaService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Dokumen BC 2.3 - pemasukan barang impor ke kawasan berikat (spesifikasi: docs/documentbc23_plan.md).
// SUMBER = PO (DBPO / DBPODET, tidak diubah): PO yang pemasoknya luar negeri, tidak batal, sudah
// IsOtorisasi1 dan belum punya baris di DOCUMENTBC23 (NOBUKTI) tampil di Outstanding. Satu PO = satu
// BC 2.3. Header + 2 koleksi (barang, dokumen) disimpan sekaligus dari satu JSON; dikirim ke CEISA
// lewat Bc23PayloadBuilder + CeisaService. Nama tabel di config/beacukai.php, kolom di
// docs/update-db-DBSMLNEW/beacukai/11_tabel_documentbc23.sql.
//
// JS/payload memakai key camelCase (nama logis di spesifikasi), tabel memakai kolom UPPERCASE.
// Peta di bawah = satu-satunya tempat pemetaan keduanya. Tipe:
//   str   teks NOT NULL ('' bila kosong)      strn  teks nullable ('' → null)
//   dec   desimal NOT NULL (0 bila kosong)    int   bulat nullable
//   intz  bulat NOT NULL (0 bila kosong)      date  'Y-m-d' atau null
//   seri  posisi baris 1..n (diisi server)
// Elemen ketiga (opsional) = nilai bawaan bila kosong.
class DocumentBc23Controller extends Controller
{
    private const HEADER = [
        'nomorAju' => ['NOMORAJU', 'strn'],
        'kodeKantor' => ['KODEKANTOR', 'strn'],
        'kodeKantorBongkar' => ['KODEKANTORBONGKAR', 'str'],
        'kodeTujuanTpb' => ['KODETUJUANTPB', 'str'],
        'nomorDokumenPabean' => ['NOMORDOKUMENPABEAN', 'strn'],
        'tanggalDokumenPabean' => ['TANGGALDOKUMENPABEAN', 'date'],
        'nomorBc11' => ['NOMORBC11', 'strn'],
        'tanggalBc11' => ['TANGGALBC11', 'date'],
        'posBc11' => ['POSBC11', 'strn'],
        'subPosBc11' => ['SUBPOSBC11', 'strn'],
        'kodePelMuat' => ['KODEPELMUAT', 'str'],
        'kodePelTransit' => ['KODEPELTRANSIT', 'str'],
        'kodePelBongkar' => ['KODEPELBONGKAR', 'str'],
        'kodeTps' => ['KODETPS', 'str'],
        'ketTps' => ['KETTPS', 'strn'],
        'tanggalTiba' => ['TANGGALTIBA', 'date'],
        'kodeValuta' => ['KODEVALUTA', 'str'],
        'ndpbm' => ['NDPBM', 'dec'],
        'kodeIncoterm' => ['KODEINCOTERM', 'str'],
        'fob' => ['FOB', 'dec'],
        'freight' => ['FREIGHT', 'dec'],
        'asuransi' => ['ASURANSI', 'dec'],
        'kodeAsuransi' => ['KODEASURANSI', 'str', 'LN'],
        'cif' => ['CIF', 'dec'],
        'bruto' => ['BRUTO', 'dec'],
        'netto' => ['NETTO', 'dec'],
        'nomorIdentitasEntitas1' => ['NOMORIDENTITASENTITAS1', 'strn'],
        'namaEntitas1' => ['NAMAENTITAS1', 'str'],
        'alamatEntitas1' => ['ALAMATENTITAS1', 'str'],
        'nomorIjinEntitas1' => ['NOMORIJINENTITAS1', 'strn'],
        'nibEntitas1' => ['NIBENTITAS1', 'strn'],
        'namaEntitas2' => ['NAMAENTITAS2', 'str'],
        'alamatEntitas2' => ['ALAMATENTITAS2', 'str'],
        'kodeNegaraEntitas2' => ['KODENEGARAENTITAS2', 'str'],
        'nomorIdentitasEntitas3' => ['NOMORIDENTITASENTITAS3', 'strn'],
        'namaEntitas3' => ['NAMAENTITAS3', 'str'],
        'alamatEntitas3' => ['ALAMATENTITAS3', 'str'],
        'selectEntitas1' => ['SELECTENTITAS1', 'int'],
        'selectEntitas2' => ['SELECTENTITAS2', 'int'],
        'selectEntitas3' => ['SELECTENTITAS3', 'int'],
        'kodeCaraAngkut' => ['KODECARAANGKUT', 'str', '1'],
        'namaPengangkut' => ['NAMAPENGANGKUT', 'str'],
        'nomorPengangkut' => ['NOMORPENGANGKUT', 'str'],
        'kodeBendera' => ['KODEBENDERA', 'str'],
        'jumlahKemasan' => ['JUMLAHKEMASAN', 'intz'],
        'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'str'],
        'merkKemasan' => ['MERKKEMASAN', 'str'],
        'nomorKontainer' => ['NOMORKONTAINER', 'strn'],
        'kodeUkuranKontainer' => ['KODEUKURANKONTAINER', 'str'],
        'kodeTipeKontainer' => ['KODETIPEKONTAINER', 'str'],
        'kodeJenisKontainer' => ['KODEJENISKONTAINER', 'str'],
        'kotaTtd' => ['KOTATTD', 'strn'],
        'tanggalTtd' => ['TANGGALTTD', 'date'],
        'namaTtd' => ['NAMATTD', 'strn'],
        'jabatanTtd' => ['JABATANTTD', 'strn'],
    ];

    // Koleksi anak: [key config tabel, kolom urutan baca, peta kolom].
    private const CHILDREN = [
        'barang' => ['bc23_barang', 'SERIBARANG', [
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
            'kodeJenisKemasan' => ['KODEJENISKEMASAN', 'str'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str', '23'],
            'netto' => ['NETTO', 'dec'],
            'cif' => ['CIF', 'dec'],
            'cifRupiah' => ['CIFRUPIAH', 'dec'],
            'ndpbm' => ['NDPBM', 'dec'],
            'fob' => ['FOB', 'dec'],
            'asuransi' => ['ASURANSI', 'dec'],
            'freight' => ['FREIGHT', 'dec'],
            'kodeKategoriBarang' => ['KODEKATEGORIBARANG', 'str'],
            'kodeNegaraAsal' => ['KODENEGARAASAL', 'str'],
            'keteranganDokumen' => ['KETERANGANDOKUMEN', 'str'],
            'persenBM' => ['PERSENBM', 'dec'],
            'persenPPN' => ['PERSENPPN', 'dec'],
            'persenPPh' => ['PERSENPPH', 'dec'],
            'nilaiBM' => ['NILAIBM', 'dec'],
            'nilaiPPN' => ['NILAIPPN', 'dec'],
            'nilaiPPh' => ['NILAIPPH', 'dec'],
        ]],
        'dokumen' => ['bc23_dokumen', 'SERIDOKUMEN', [
            'seriDokumen' => ['SERIDOKUMEN', 'seri'],
            'kodeDokumen' => ['KODEDOKUMEN', 'str'],
            'nomorDokumen' => ['NOMORDOKUMEN', 'strn'],
            'tanggalDokumen' => ['TANGGALDOKUMEN', 'date'],
        ]],
    ];

    // Daftar kode tetap (docs/documentbc23_plan.md Lampiran A). Incoterm: daftar Incoterms 2020
    // (belum ada master incoterm; isian tetap bisa diketik bebas).
    private const KODE = [
        'tujuanTpb' => ['1' => 'Kawasan Berikat', '2' => 'Gudang Berikat', '3' => 'TPPB', '4' => 'TBB', '5' => 'TLB',
            '6' => 'KDUB', '7' => 'Lainnya'],
        'caraAngkut' => ['1' => 'Laut', '2' => 'Kereta Api', '3' => 'Darat', '4' => 'Udara', '5' => 'Pos', '6' => 'Multimoda',
            '7' => 'Instalasi/Pipa', '8' => 'Perairan', '9' => 'Lainnya'],
        'asuransi' => ['LN' => 'Luar Negeri', 'DN' => 'Dalam Negeri'],
        'jenisMuatan' => ['4' => 'Empty', '7' => 'LCL', '8' => 'FCL'],
        'incoterm' => ['EXW', 'FCA', 'FAS', 'FOB', 'CFR', 'CIF', 'CPT', 'CIP', 'DAP', 'DPU', 'DDP'],
    ];

    // Waktu tunggu kirim BC 2.3 (BC 2.6.1 memakai 30) - docs/documentbc23_plan.md §8.
    private const TIMEOUT_KIRIM = 60;

    // Pemeriksaan saat KIRIM terhadap skema resmi docs/bc_schema/bc23.json (bukan saat simpan). Kolom teks di
    // bawah ini tidak boleh kosong walau skema hanya menuntut tipe string (keputusan user); yang boleh kosong:
    // Pelabuhan Transit, NIB, merk kemasan, nama / nomor / bendera pengangkut.
    private const WAJIB_ISI = [
        'kodeKantor', 'kodeKantorBongkar', 'kodePelMuat', 'kodePelBongkar', 'kodeTps', 'kodeTujuanTpb', 'kodeValuta',
        'kodeIncoterm', 'nomorBc11', 'posBc11', 'subposBc11', 'kotaTtd', 'namaTtd', 'jabatanTtd',
        'barang.kodeBarang', 'barang.uraian', 'barang.posTarif', 'barang.kodeSatuanBarang', 'barang.kodeJenisKemasan',
        'barang.kodeKategoriBarang', 'barang.kodeNegaraAsal',
        'entitas.namaEntitas', 'entitas.alamatEntitas', 'entitas.nomorIdentitas',
        'dokumen.kodeDokumen', 'dokumen.nomorDokumen',
    ];

    public function __construct(private Bc23PayloadBuilder $builder, private CeisaService $ceisa, private CeisaSchemaValidator $skema) {}

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

        return view('documentbc.documentbc23', [
            'periode' => $periode,
            'menul0' => $menul0,
            'date1' => $date1,
            'date2' => $date2,
            'akses' => $akses,
            'kodeKantor' => config('beacukai.kode_kantor'),
            'kodeKantorBongkar' => config('beacukai.kode_kantor_bongkar'),
            'kode' => self::KODE,
            'entitas' => $this->daftarEntitas(),
            // kurs = DBVALAS.KURS: isian awal NDPBM bila kode valuta dokumen ada di master valas.
            'valuta' => DB::connection('SML')->select('SELECT KODEVLS AS kode, NAMAVLS AS nama, KURS AS kurs FROM dbVALAS ORDER BY KODEVLS'),
            'ukuranKontainer' => $this->daftarKontainer('ukuran_kontainer', 'KODEUKURANKONTAINER', 'NAMAUKURANKONTAINER'),
            'tipeKontainer' => $this->daftarKontainer('tipe_kontainer', 'KODETIPEKONTAINER', 'NAMATIPEKONTAINER'),
            'lookup' => [
                'negara' => DB::connection('SML')->select('SELECT KODENEGARA AS kode, NAMANEGARA AS nama FROM '.config('beacukai.tables.negara').' ORDER BY KODENEGARA'),
                'pelabuhan' => $this->daftarKode('pelabuhan'),
                'satuan' => $this->daftarKode('satuan'),
                'kemasan' => $this->daftarKode('kemasan'),
                'jenisDokumen' => $this->daftarKode('jenis_dokumen'),
                'kategoriBarang' => $this->daftarKode('kategori_barang'),
            ],
        ]);
    }

    // Outstanding: PO pemasok luar negeri yang belum punya BC 2.3. DBPO / DBPODET hanya dibaca.
    public function loadOutstanding()
    {
        return DB::connection('SML')->select('
            SELECT a.NOBUKTI AS nobukti, a.TANGGAL AS tanggal, a.KODESUPP AS kodeSupp, b.NAMACUSTSUPP AS namaSupp,
                   b.NEGARA AS negara, a.KODEVLS AS kodeValuta,
                   (SELECT COUNT(*) FROM DBPODET d WHERE d.NOBUKTI = a.NOBUKTI AND ISNULL(d.Isbatal, 0) = 0) AS jumlahBaris
            FROM DBPO a
            JOIN DBCUSTSUPP b ON b.KODECUSTSUPP = a.KODESUPP
            WHERE b.NEGARA <> \'INDONESIA\'
              AND ISNULL(a.IsBatal, 0) = 0
              AND a.IsOtorisasi1 = 1
              AND NOT EXISTS (SELECT 1 FROM '.$this->tabel('bc23').' x WHERE x.NOBUKTI = a.NOBUKTI)
            ORDER BY a.TANGGAL DESC, a.NOBUKTI DESC');
    }

    // Daftar transaksi: rentang tanggal pada TANGGALTTD (dokumen tanpa TTD memakai tanggal buat).
    public function loadAll(Request $req)
    {
        $date1 = $this->tanggal($req->date1) ?? date('Y-m-01');
        $date2 = $this->tanggal($req->date2) ?? date('Y-m-t');

        return DB::connection('SML')->select('
            SELECT ID AS id, NOBUKTI AS revDoc, NOMORAJU AS nomorAju, TANGGALTTD AS tanggalTtd,
                   NAMAENTITAS2 AS namaEntitas2, ISNULL(SENT, 0) AS sent, TGLKIRIM AS tglKirim
            FROM '.$this->tabel('bc23').'
            WHERE ISNULL(TANGGALTTD, CAST(TGLBUAT AS date)) BETWEEN :date1 AND :date2
            ORDER BY ID DESC', ['date1' => $date1, 'date2' => $date2]);
    }

    // Isi PO untuk Detail (baca saja) dan untuk mengisi awal form Tambah: header PO + baris DBPODET.
    public function spSumber(Request $req)
    {
        $po = DB::connection('SML')->selectOne('
            SELECT a.NOBUKTI, a.TANGGAL, a.KODESUPP, b.NAMACUSTSUPP, b.NEGARA, a.KODEVLS, a.KETERANGAN,
                   a.IsBatal, a.IsOtorisasi1
            FROM DBPO a
            LEFT JOIN DBCUSTSUPP b ON b.KODECUSTSUPP = a.KODESUPP
            WHERE a.NOBUKTI = ?', [(string) $req->nobukti]);
        if (! $po) {
            return response()->json(['message' => 'PO tidak ditemukan.'], 404);
        }

        $dipakai = (bool) DB::connection('SML')->selectOne(
            'SELECT 1 AS ada FROM '.$this->tabel('bc23').' WHERE NOBUKTI = ?', [$po->NOBUKTI]
        );

        $rows = DB::connection('SML')->select('
            SELECT c.URUT, c.KODEBRG, c.NamaBrg, g.NAMABRG, c.QNT, c.QntBatal, c.SATUAN, c.SUBTOTAL, g.Berat
            FROM DBPODET c
            LEFT JOIN DBBARANG g ON g.KODEBRG = c.KODEBRG
            WHERE c.NOBUKTI = ? AND ISNULL(c.Isbatal, 0) = 0
            ORDER BY c.URUT', [$po->NOBUKTI]);

        // Isian awal barang: baris dengan sisa qty <= 0 (dibatalkan lewat QntBatal) dilewati; CIF
        // (SUBTOTAL, dalam valuta PO) diperkecil sebanding bila sebagian qty dibatalkan; netto =
        // DBBARANG.Berat x qty (0 bila berat belum diisi). Selebihnya diisi user.
        $barang = [];
        foreach ($rows as $r) {
            $qnt = (float) $r->QNT;
            $qty = $qnt - (float) $r->QntBatal;
            if ($qty <= 0) {
                continue;
            }
            $subtotal = (float) $r->SUBTOTAL;
            $barang[] = [
                'kodeBarang' => (string) $r->KODEBRG,
                'uraian' => mb_substr(trim((string) $r->NamaBrg) !== '' ? trim((string) $r->NamaBrg) : trim((string) $r->NAMABRG), 0, 100),
                'kodeSatuanBarang' => trim((string) $r->SATUAN),
                'jumlahSatuan' => $qty,
                'cif' => round((float) $r->QntBatal > 0 && $qnt > 0 ? $subtotal * $qty / $qnt : $subtotal, 4),
                'netto' => round((float) $r->Berat * $qty, 4),
            ];
        }

        $kurs = DB::connection('SML')->selectOne('SELECT KURS FROM dbVALAS WHERE KODEVLS = ?', [$po->KODEVLS]);
        $entitas2 = DB::connection('SML')->selectOne(
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
                'kodeValuta' => $po->KODEVLS,
                'keterangan' => trim((string) $po->KETERANGAN),
                'dipakai' => $dipakai,
            ],
            'header' => [
                'kodeValuta' => trim((string) $po->KODEVLS),
                'ndpbm' => $kurs ? (float) $kurs->KURS : 0,
                'selectEntitas2' => $entitas2 ? (int) $entitas2->id : null,
            ],
            'barang' => $barang,
        ];
    }

    // Pemilih HS (POSTARIFHS): 50 kode pertama yang cocok. Tarif BM / PPN / PPh disalin ke barang
    // saat dipilih (dikirim di persenBM / persenPPN / persenPPh).
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
        $nobukti = trim((string) ($data['nobukti'] ?? ''));
        if (! $data || $nobukti === '') {
            return response()->json(['message' => 'Data tidak valid.'], 422);
        }

        $header = $this->normalisasi(self::HEADER, $data['header'] ?? []);
        $children = $this->normalisasiAnak($data, $header);

        return DB::connection('SML')->transaction(function () use ($nobukti, $header, $children) {
            // Satu PO satu BC 2.3; UPDLOCK menahan sampai commit supaya dua user tidak memakai PO yang sama.
            $sudah = DB::connection('SML')->selectOne(
                'SELECT ID FROM '.$this->tabel('bc23').' WITH (UPDLOCK, HOLDLOCK) WHERE NOBUKTI = ?', [$nobukti]
            );
            if ($sudah) {
                return response()->json(['message' => "PO {$nobukti} sudah dibuatkan BC 2.3."], 409);
            }
            if (! $this->poBolehDipakai($nobukti)) {
                return response()->json(['message' => "PO {$nobukti} tidak memenuhi syarat BC 2.3 (pemasok luar negeri, tidak batal, sudah otorisasi)."], 422);
            }

            $kolom = $this->keKolom(self::HEADER, $header) + [
                'NOBUKTI' => $nobukti,
                'BCTYPE' => '23',
                'USERBUAT' => \Auth::user()->username,
            ];
            $id = (int) $this->sisipkan($this->tabel('bc23'), $kolom, true);
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
        $children = $this->normalisasiAnak($data, $header);

        return DB::connection('SML')->transaction(function () use ($id, $header, $children) {
            if ($tolak = $this->cekBolehUbah($id)) {
                return $tolak;
            }

            // Header ditimpa (NOBUKTI, BCTYPE & SENT tidak diubah), anak diganti seluruhnya.
            $kolom = $this->keKolom(self::HEADER, $header);
            $set = implode(', ', array_map(fn ($c) => "{$c} = ?", array_keys($kolom)));
            DB::connection('SML')->update(
                'UPDATE '.$this->tabel('bc23')." SET {$set}, USERUBAH = ?, TGLUBAH = GETDATE() WHERE ID = ?",
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
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel('bc23').' WHERE ID = ?', [$id]);

            // PO kembali ke Outstanding otomatis (barisnya di DOCUMENTBC23 sudah tidak ada).
            return response()->json(['message' => 'Data telah dihapus.']);
        });
    }

    // Kirim ke CEISA (docs/documentbc23_plan.md §8).
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

        // Q-b: Villiger menganggap semua HTTP 2xx berhasil; di sini harus status OK.
        if (($json['status'] ?? null) !== 'OK') {
            return response()->json([
                'message' => 'Pengiriman gagal.', 'details' => CeisaService::details($raw), 'response' => $json,
            ], 400);
        }

        $idHeader = CeisaService::cariNilai($json, ['idHeader']);
        DB::connection('SML')->update(
            'UPDATE '.$this->tabel('bc23').' SET SENT = 1, IDHEADER = ?, TGLKIRIM = GETDATE(), USERKIRIM = ? WHERE ID = ?',
            [$idHeader === null ? null : mb_substr((string) $idHeader, 0, 100), \Auth::user()->username, $id]
        );

        return response()->json([
            'message' => is_string($json['message'] ?? null) && $json['message'] !== '' ? $json['message'] : 'Data berhasil dikirim.',
            'idHeader' => $idHeader,
        ]);
    }

    // Skema resmi + aturan BC 2.3 di luar skema: minimal 1 barang, dokumen minimal Invoice (380) lalu BL/AWB (705/740),
    // valuta harus sudah berupa kode ISO 3 huruf (config beacukai.valuta_iso).
    private function masalahKirim(array $payload): array
    {
        $masalah = $this->skema->masalah('bc23', $payload, [
            'bagian' => [
                'barang' => 'Barang', 'barangTarif' => ['Tarif BM', 'Tarif PPH', 'Tarif PPN'], 'dokumen' => 'Dokumen',
                'entitas' => ['Importir / Pengusaha TPB', 'Pemasok', 'Pemilik Barang'], 'kemasan' => 'Kemasan',
                'kontainer' => 'Peti Kemas', 'pengangkut' => 'Pengangkut',
            ],
            'wajibIsi' => self::WAJIB_ISI,
            'sisa' => ['barang', 'dokumen', 'kemasan', 'kontainer'],
        ]);

        if (! $payload['barang']) {
            $masalah[] = 'Barang: minimal 1 barang.';
        }
        if (count($payload['dokumen']) < 2) {
            $masalah[] = 'Dokumen: minimal 2 dokumen, Invoice (380) lalu BL/AWB (705 atau 740).';
        }
        if ($payload['kodeValuta'] !== '' && ! preg_match('/^[A-Z]{3}$/', $payload['kodeValuta'])) {
            $masalah[] = "Valuta ({$payload['kodeValuta']}): belum punya kode ISO 3 huruf; tambahkan padanannya di config/beacukai.php (valuta_iso) atau ganti valuta.";
        }

        return $masalah;
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
        $akses = app('App\Http\Controllers\GlobalController')->getAkses('', 'documentbc23');
        if (! $akses || ! $akses->HASACCESS || ! $akses->$hak) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        return null;
    }

    // Syarat sumber (tanpa cek "sudah dipakai", itu dicek terpisah): pemasok luar negeri, PO tidak
    // batal, sudah IsOtorisasi1.
    private function poBolehDipakai(string $nobukti): bool
    {
        return (bool) DB::connection('SML')->selectOne('
            SELECT 1 AS ada
            FROM DBPO a
            JOIN DBCUSTSUPP b ON b.KODECUSTSUPP = a.KODESUPP
            WHERE a.NOBUKTI = ? AND b.NEGARA <> \'INDONESIA\' AND ISNULL(a.IsBatal, 0) = 0 AND a.IsOtorisasi1 = 1', [$nobukti]);
    }

    // Dokumen yang sudah terkirim ke CEISA dikunci dari Edit/Hapus (Q-f). Dipanggil di dalam
    // transaksi; UPDLOCK menahan baris sampai commit supaya tidak bentrok dengan pengiriman.
    private function cekBolehUbah(int $id)
    {
        $doc = DB::connection('SML')->selectOne(
            'SELECT ID, ISNULL(SENT, 0) AS SENT FROM '.$this->tabel('bc23').' WITH (UPDLOCK, HOLDLOCK) WHERE ID = ?', [$id]
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
            SELECT e.id, e.NAMAENTITAS AS namaEntitas, e.ALAMATENTITAS AS alamatEntitas, e.NOMORIDENTITAS AS nomorIdentitas,
                   e.NOMORIJINIDENTITAS AS nomorIjinEntitas, e.NIBENTITAS AS nibEntitas, n.KODENEGARA AS kodeNegara
            FROM '.$this->tabel('entitas').' e
            LEFT JOIN '.$this->tabel('negara').' n ON n.id = e.IDNEGARA
            ORDER BY e.NAMAENTITAS');
    }

    // Header + koleksi anak dalam bentuk camelCase bertipe PHP (desimal float, tanggal 'Y-m-d').
    private function muatDokumen(int $id): ?array
    {
        $row = DB::connection('SML')->selectOne('SELECT * FROM '.$this->tabel('bc23').' WHERE ID = ?', [$id]);
        if (! $row) {
            return null;
        }
        $row = (array) $row;

        $header = ['id' => (int) $row['ID'], 'nobukti' => (string) $row['NOBUKTI'], 'sent' => (int) ($row['SENT'] ?? 0), 'idHeader' => $row['IDHEADER'] ?? null]
            + $this->dariBaris(self::HEADER, $row);

        $children = [];
        foreach (self::CHILDREN as $nama => [$kunciTabel, $urut, $peta]) {
            $rows = DB::connection('SML')->select('SELECT * FROM '.$this->tabel($kunciTabel)." WHERE IDBC23 = ? ORDER BY {$urut}, ID", [$id]);
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
                'str' => ($s = trim((string) $v)) === '' ? ($def[2] ?? '') : $s,
                'strn' => ($s = trim((string) $v)) === '' ? null : $s,
                'dec' => $this->desimal($v) ?? 0.0,
                'int' => $this->bulat($v),
                'intz' => $this->bulat($v) ?? 0,
                'date' => $this->tanggal($v),
            };
        }

        return $out;
    }

    private function normalisasiAnak(array $data, array $header): array
    {
        $children = [];
        foreach (self::CHILDREN as $nama => [, , $peta]) {
            $rows = is_array($data[$nama] ?? null) ? array_values($data[$nama]) : [];
            $children[$nama] = [];
            foreach ($rows as $i => $row) {
                $children[$nama][] = $this->normalisasi($peta, is_array($row) ? $row : [], $i + 1);
            }
        }

        // Q-g: total fob / asuransi / freight header disalin ke setiap barang.
        foreach ($children['barang'] as &$barang) {
            $barang['fob'] = $header['fob'];
            $barang['asuransi'] = $header['asuransi'];
            $barang['freight'] = $header['freight'];
        }
        unset($barang);

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
                $this->sisipkan($this->tabel($kunciTabel), ['IDBC23' => $id] + $this->keKolom($peta, $row));
            }
        }
    }

    private function hapusAnak(int $id): void
    {
        foreach (self::CHILDREN as [$kunciTabel]) {
            DB::connection('SML')->delete('DELETE FROM '.$this->tabel($kunciTabel).' WHERE IDBC23 = ?', [$id]);
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
