<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Amortisasi.
 *
 * Header DBAMORTISASI (Kode, keterangan, Nilai, Tipe, Tanggal, lama, Metode, Persen) dipecah
 * menjadi `lama` baris jadwal di DBAMORTISASIDET (kode, Tanggal, Nilai, Status).
 *
 * - Tipe   : 0 = Bulan (jadwal tiap bulan), 1 = Tahun (jadwal tiap tahun).
 * - Metode : 0 / NULL = Garis Lurus, 1 = Saldo Menurun.
 * - Persen : tarif per tahun (%), hanya diisi untuk Saldo Menurun (NULL untuk Garis Lurus).
 * - Status : 0 = Belum, 1 = Tereksekusi, 2 = Batal.
 *
 * DBAMORTISASIDET tidak punya primary key maupun kolom urut. Satu baris jadwal
 * dikenali dari kode + Tanggal (unik karena jarak antarbaris selalu 1 bulan / 1 tahun).
 *
 * Semua tanggal dikirim ke SQL Server dalam format 'Ymd' (mis. 20261105) karena hanya
 * format itu yang dibaca sama oleh tipe datetime di setting bahasa server mana pun.
 */
class AmortisasiController extends Controller
{
    // href di DBMENUWEB. Dipatok (bukan $req->path()) supaya hak akses tetap terbaca
    // dari route AJAX seperti /amortisasispadd.
    const HREF = 'amortisasi';

    const KODEMENU = '0209';

    const MAX_LAMA = 600;

    private function akses()
    {
        return app('App\Http\Controllers\GlobalController')->getAkses(self::KODEMENU, self::HREF);
    }

    // Hak per tombol: ISTAMBAH, ISKOREKSI, ISHAPUS, IsOtorisasi1 (eksekusi), IsBatal.
    private function punyaHak($kolom)
    {
        $akses = $this->akses();

        return $akses && $akses->HASACCESS && (int) $akses->{$kolom} === 1;
    }

    private function gagal($pesan, $status = 422)
    {
        return response()->json(['message' => $pesan], $status);
    }

    // Tanggal 'Y-m-d' dari request -> Carbon, atau null kalau tidak valid.
    private function tanggalDariInput($nilai)
    {
        $nilai = (string) $nilai;
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $nilai)) {
            return null;
        }
        try {
            $tgl = Carbon::createFromFormat('!Y-m-d', $nilai);
        } catch (\Exception $e) {
            return null;
        }

        return $tgl->format('Y-m-d') === $nilai ? $tgl : null;
    }

    // Rentang default: tanggal 1 bulan berjalan s/d hari ini.
    private function rentangTanggal(Request $req, $awalKey = 'tglawal', $akhirKey = 'tglakhir')
    {
        $awal = $this->tanggalDariInput($req->input($awalKey)) ?: Carbon::today()->startOfMonth();
        $akhir = $this->tanggalDariInput($req->input($akhirKey)) ?: Carbon::today();
        if ($awal->gt($akhir)) {
            $akhir = $awal->copy();
        }

        return [$awal, $akhir];
    }

    /**
     * Baris jadwal yang dibuat dari satu header.
     *
     * Tanggal baris ke-i = tanggal header + i bulan/tahun, selalu dihitung dari tanggal
     * awal (bukan dari baris sebelumnya), supaya 31 Jan -> 28 Feb -> 31 Mar tidak
     * bergeser menjadi 28 Mar.
     *
     * Nilai dihitung dalam sen sehingga jumlah semua baris persis sama dengan Nilai header:
     * - Garis Lurus (metode 0): tiap baris = floor(total / lama), sisa pembulatan masuk
     *   ke baris terakhir.
     * - Saldo Menurun (metode 1): lihat nilaiSaldoMenurun().
     * Aturan yang sama dipakai preview di amortisasi.blade.php (amBuatJadwal).
     */
    private function buatJadwal($nilai, $tipe, Carbon $tanggal, $lama, $metode = 0, $persen = null)
    {
        $totalSen = (int) round($nilai * 100);
        $perBaris = intdiv($totalSen, $lama);
        $nilaiMenurun = (int) $metode === 1 ? $this->nilaiSaldoMenurun($nilai, $tipe, $lama, $persen) : null;
        $jadwal = [];

        for ($i = 0; $i < $lama; $i++) {
            $tgl = (int) $tipe === 1
                ? $tanggal->copy()->addYearsNoOverflow($i)
                : $tanggal->copy()->addMonthsNoOverflow($i);

            if ($nilaiMenurun !== null) {
                $nilaiBaris = $nilaiMenurun[$i];
            } else {
                $sen = ($i === $lama - 1) ? $totalSen - $perBaris * ($lama - 1) : $perBaris;
                $nilaiBaris = $sen / 100;
            }

            $jadwal[] = ['tanggal' => $tgl, 'nilai' => $nilaiBaris];
        }

        return $jadwal;
    }

    /**
     * Nilai tiap baris metode Saldo Menurun (dalam sen, hasil dibagi 100).
     *
     * Persen = tarif per tahun. Tiap baris kecuali yang terakhir = sisa x persen / 1200
     * (Tipe Bulan) atau sisa x persen / 100 (Tipe Tahun), dibulatkan ke sen (half-up)
     * dan dibatasi paling besar sisa. Baris terakhir mengambil seluruh sisa, sehingga
     * jumlahnya persis sama dengan Nilai header.
     * Hitungannya bilangan bulat murni (persen dalam basis point, pembagian + sisa bagi)
     * supaya hasilnya identik dengan amBuatJadwal() di amortisasi.blade.php (BigInt).
     */
    private function nilaiSaldoMenurun($nilai, $tipe, $lama, $persen)
    {
        $p = (int) round((float) $persen * 100);
        $den = (int) $tipe === 1 ? 10000 : 120000;
        $sisa = (int) round($nilai * 100);
        $hasil = [];

        for ($i = 0; $i < $lama; $i++) {
            if ($i === $lama - 1) {
                $sen = $sisa;
            } else {
                $num = $sisa * $p;
                $sen = intdiv($num, $den);
                if (($num - $sen * $den) * 2 >= $den) {
                    $sen++;
                }
                $sen = min($sen, $sisa);
            }

            $sisa -= $sen;
            $hasil[] = $sen / 100;
        }

        return $hasil;
    }

    // Validasi isian form Tambah / Koreksi. Hasil: [data, pesan error].
    private function validasiForm(Request $req)
    {
        $keterangan = trim((string) $req->input('keterangan'));
        $nilai = round((float) $req->input('nilai'), 2);
        $tipe = (string) $req->input('tipe');
        $tanggal = $this->tanggalDariInput($req->input('tanggal'));
        $lama = filter_var($req->input('lama'), FILTER_VALIDATE_INT);
        $metode = (string) $req->input('metode');
        $persen = null;

        if ($keterangan === '') {
            return [null, 'Keterangan harus diisi'];
        }
        if (mb_strlen($keterangan) > 50) {
            return [null, 'Keterangan maksimal 50 karakter'];
        }
        if (! is_numeric($req->input('nilai')) || $nilai <= 0) {
            return [null, 'Nilai harus lebih dari 0'];
        }
        if ($tipe !== '0' && $tipe !== '1') {
            return [null, 'Tipe tidak valid'];
        }
        if (! $tanggal) {
            return [null, 'Tanggal tidak valid'];
        }
        if ($lama === false || $lama < 1 || $lama > self::MAX_LAMA) {
            return [null, 'Lama harus antara 1 dan '.self::MAX_LAMA];
        }
        if ($metode !== '0' && $metode !== '1') {
            return [null, 'Metode tidak valid'];
        }
        if ($metode === '1') {
            // Hitungan sen x basis point memakai int 64-bit; di atas batas ini bisa meluap.
            if ($nilai > 999999999999.99) {
                return [null, 'Nilai maksimal 999,999,999,999.99 untuk Saldo Menurun'];
            }
            $persenInput = $req->input('persen');
            if (! is_numeric($persenInput) || (float) $persenInput <= 0 || (float) $persenInput > 100) {
                return [null, 'Persen harus lebih dari 0 dan maksimal 100'];
            }
            $persen = round((float) $persenInput, 2);
            if ($persen <= 0) {
                return [null, 'Persen harus lebih dari 0 dan maksimal 100'];
            }
        } elseif ((int) round($nilai * 100) < $lama) {
            // Hanya Garis Lurus yang membagi rata; Saldo Menurun boleh menghasilkan baris 0.
            return [null, 'Nilai terlalu kecil untuk dibagi '.$lama.' baris'];
        }

        return [[
            'keterangan' => $keterangan,
            'nilai' => $nilai,
            'tipe' => (int) $tipe,
            'tanggal' => $tanggal,
            'lama' => $lama,
            'metode' => (int) $metode,
            'persen' => $persen,
        ], null];
    }

    private function simpanJadwal($kode, array $data)
    {
        $jadwal = $this->buatJadwal(
            $data['nilai'], $data['tipe'], $data['tanggal'], $data['lama'], $data['metode'], $data['persen']
        );

        foreach ($jadwal as $baris) {
            DB::connection('SML')->insert(
                'insert into DBAMORTISASIDET (kode, Tanggal, Nilai, Status) values (:kode, :tanggal, :nilai, 0)',
                ['kode' => $kode, 'tanggal' => $baris['tanggal']->format('Ymd'), 'nilai' => $baris['nilai']]
            );
        }
    }

    // Kode baru: AM + yymm tanggal header + nomor urut 4 digit per bulan (AM26110001).
    // Dipanggil di dalam transaksi; UPDLOCK + HOLDLOCK mencegah dua user mendapat nomor sama.
    private function kodeBaru(Carbon $tanggal)
    {
        $prefix = 'AM'.$tanggal->format('ym');

        $row = DB::connection('SML')->select(
            'select max(Kode) as kode from DBAMORTISASI with (updlock, holdlock) where Kode like :prefix',
            ['prefix' => $prefix.'%']
        );

        $terakhir = $row[0]->kode ?? null;
        $urut = $terakhir ? ((int) substr($terakhir, strlen($prefix))) + 1 : 1;

        if ($urut > 9999) {
            throw new \RuntimeException('Nomor amortisasi bulan '.$tanggal->format('m/Y').' sudah penuh');
        }

        return $prefix.str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }

    // Jumlah baris jadwal yang sudah tidak Belum (tereksekusi / batal).
    private function jumlahDiproses($kode)
    {
        $row = DB::connection('SML')->select(
            'select count(*) as n from DBAMORTISASIDET where kode = :kode and isnull(Status, 0) <> 0',
            ['kode' => $kode]
        );

        return (int) $row[0]->n;
    }

    public function index(Request $req)
    {
        $akses = $this->akses();
        if (! $akses || ! $akses->HASACCESS) {
            return redirect('/home');
        }

        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

        return view('accounting.amortisasi', [
            'menul0' => $menul0,
            'periode' => $periode,
            'akses' => $akses,
            'tglAwal' => Carbon::today()->startOfMonth()->format('Y-m-d'),
            'tglAkhir' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    // Tab "Daftar Amortisasi": header pada rentang DBAMORTISASI.Tanggal + progres jadwalnya.
    public function loadAll(Request $req)
    {
        [$awal, $akhir] = $this->rentangTanggal($req);

        return DB::connection('SML')->select('
            select A.Kode, A.keterangan as Keterangan, A.Nilai, isnull(A.Tipe, 0) as Tipe,
                   convert(varchar(10), A.Tanggal, 23) as Tanggal, A.lama as Lama,
                   isnull(A.Metode, 0) as Metode, A.Persen,
                   isnull(D.JmlJadwal, 0) as JmlJadwal,
                   isnull(D.JmlEksekusi, 0) as JmlEksekusi,
                   isnull(D.JmlBatal, 0) as JmlBatal
            from DBAMORTISASI A
            left join (
                select kode, count(*) as JmlJadwal,
                       sum(case when Status = 1 then 1 else 0 end) as JmlEksekusi,
                       sum(case when Status = 2 then 1 else 0 end) as JmlBatal
                from DBAMORTISASIDET
                group by kode
            ) D on D.kode = A.Kode
            where A.Tanggal >= :awal and A.Tanggal < dateadd(day, 1, cast(:akhir as datetime))
            order by A.Tanggal desc, A.Kode desc', [
            'awal' => $awal->format('Ymd'),
            'akhir' => $akhir->format('Ymd'),
        ]);
    }

    // Tab "Jadwal Amortisasi": baris jadwal (semua status) pada rentang DBAMORTISASIDET.Tanggal.
    // Sisa = nilai buku rencana setelah baris ini (Nilai header - jumlah baris s/d tanggal ini).
    // Dihitung dari seluruh jadwal kode itu dan mengabaikan Status (Batal tetap dihitung).
    // Subquery berkorelasi karena SQL Server 2008 R2 tidak punya SUM() OVER (ORDER BY).
    public function loadDetail(Request $req)
    {
        [$awal, $akhir] = $this->rentangTanggal($req);

        return DB::connection('SML')->select('
            select D.kode as Kode, A.keterangan as Keterangan,
                   convert(varchar(10), D.Tanggal, 23) as Tanggal, D.Nilai,
                   A.Nilai - (select sum(X.Nilai) from DBAMORTISASIDET X
                              where X.kode = D.kode and X.Tanggal <= D.Tanggal) as Sisa,
                   isnull(D.Status, 0) as Status
            from DBAMORTISASIDET D
            left join DBAMORTISASI A on A.Kode = D.kode
            where D.Tanggal >= :awal and D.Tanggal < dateadd(day, 1, cast(:akhir as datetime))
            order by D.Tanggal, D.kode', [
            'awal' => $awal->format('Ymd'),
            'akhir' => $akhir->format('Ymd'),
        ]);
    }

    // Satu header + seluruh jadwalnya (untuk form Koreksi).
    public function spDetail(Request $req)
    {
        $header = DB::connection('SML')->select('
            select Kode, keterangan as Keterangan, Nilai, isnull(Tipe, 0) as Tipe,
                   convert(varchar(10), Tanggal, 23) as Tanggal, lama as Lama,
                   isnull(Metode, 0) as Metode, Persen
            from DBAMORTISASI where Kode = :kode', ['kode' => $req->input('kode')]);

        if (! $header) {
            return $this->gagal('Data amortisasi tidak ditemukan', 404);
        }

        $detail = DB::connection('SML')->select('
            select convert(varchar(10), Tanggal, 23) as Tanggal, Nilai, isnull(Status, 0) as Status
            from DBAMORTISASIDET where kode = :kode order by Tanggal', ['kode' => $req->input('kode')]);

        return ['header' => $header[0], 'detail' => $detail];
    }

    public function spAdd(Request $req)
    {
        if (! $this->punyaHak('ISTAMBAH')) {
            return $this->gagal('Anda tidak punya hak tambah', 403);
        }

        [$data, $pesan] = $this->validasiForm($req);
        if ($pesan) {
            return $this->gagal($pesan);
        }

        try {
            $kode = DB::connection('SML')->transaction(function () use ($data) {
                $kode = $this->kodeBaru($data['tanggal']);

                DB::connection('SML')->insert(
                    'insert into DBAMORTISASI (Kode, keterangan, Nilai, Tipe, Tanggal, lama, Metode, Persen)
                     values (:kode, :keterangan, :nilai, :tipe, :tanggal, :lama, :metode, :persen)', [
                        'kode' => $kode,
                        'keterangan' => $data['keterangan'],
                        'nilai' => $data['nilai'],
                        'tipe' => $data['tipe'],
                        'tanggal' => $data['tanggal']->format('Ymd'),
                        'lama' => $data['lama'],
                        'metode' => $data['metode'],
                        'persen' => $data['persen'],
                    ]);

                $this->simpanJadwal($kode, $data);

                return $kode;
            });
        } catch (\RuntimeException $e) {
            return $this->gagal($e->getMessage());
        }

        return ['ok' => 1, 'kode' => $kode];
    }

    // Koreksi: hanya boleh selama semua jadwal masih Belum. Jadwal lama dihapus dan
    // dibuat ulang dari isian baru. Kode tidak berubah walaupun bulan tanggalnya berubah.
    public function spEdit(Request $req)
    {
        if (! $this->punyaHak('ISKOREKSI')) {
            return $this->gagal('Anda tidak punya hak koreksi', 403);
        }

        $kode = (string) $req->input('kode');

        [$data, $pesan] = $this->validasiForm($req);
        if ($pesan) {
            return $this->gagal($pesan);
        }

        return DB::connection('SML')->transaction(function () use ($kode, $data) {
            $ada = DB::connection('SML')->select(
                'select Kode from DBAMORTISASI with (updlock) where Kode = :kode', ['kode' => $kode]
            );
            if (! $ada) {
                return $this->gagal('Data amortisasi tidak ditemukan', 404);
            }
            if ($this->jumlahDiproses($kode) > 0) {
                return $this->gagal('Sudah ada jadwal yang tereksekusi / batal, tidak bisa dikoreksi');
            }

            DB::connection('SML')->update(
                'update DBAMORTISASI set keterangan = :keterangan, Nilai = :nilai, Tipe = :tipe,
                 Tanggal = :tanggal, lama = :lama, Metode = :metode, Persen = :persen
                 where Kode = :kode', [
                    'keterangan' => $data['keterangan'],
                    'nilai' => $data['nilai'],
                    'tipe' => $data['tipe'],
                    'tanggal' => $data['tanggal']->format('Ymd'),
                    'lama' => $data['lama'],
                    'metode' => $data['metode'],
                    'persen' => $data['persen'],
                    'kode' => $kode,
                ]);

            DB::connection('SML')->delete('delete from DBAMORTISASIDET where kode = :kode', ['kode' => $kode]);
            $this->simpanJadwal($kode, $data);

            return ['ok' => 1, 'kode' => $kode];
        });
    }

    public function spDelete(Request $req)
    {
        if (! $this->punyaHak('ISHAPUS')) {
            return $this->gagal('Anda tidak punya hak hapus', 403);
        }

        $kode = (string) $req->input('kode');

        return DB::connection('SML')->transaction(function () use ($kode) {
            $ada = DB::connection('SML')->select(
                'select Kode from DBAMORTISASI with (updlock) where Kode = :kode', ['kode' => $kode]
            );
            if (! $ada) {
                return $this->gagal('Data amortisasi tidak ditemukan', 404);
            }
            if ($this->jumlahDiproses($kode) > 0) {
                return $this->gagal('Sudah ada jadwal yang tereksekusi / batal, tidak bisa dihapus');
            }

            DB::connection('SML')->delete('delete from DBAMORTISASIDET where kode = :kode', ['kode' => $kode]);
            DB::connection('SML')->delete('delete from DBAMORTISASI where Kode = :kode', ['kode' => $kode]);

            return ['ok' => 1];
        });
    }

    // Tombol centang: satu baris jadwal Belum -> Tereksekusi.
    public function spEksekusi(Request $req)
    {
        if (! $this->punyaHak('IsOtorisasi1')) {
            return $this->gagal('Anda tidak punya hak eksekusi', 403);
        }

        $tanggal = $this->tanggalDariInput($req->input('tanggal'));
        if (! $tanggal) {
            return $this->gagal('Tanggal tidak valid');
        }

        $n = DB::connection('SML')->update(
            'update DBAMORTISASIDET set Status = 1
             where kode = :kode and Tanggal = :tanggal and isnull(Status, 0) = 0', [
                'kode' => (string) $req->input('kode'),
                'tanggal' => $tanggal->format('Ymd'),
            ]);

        if ($n === 0) {
            return $this->gagal('Jadwal sudah tidak berstatus Belum. Data dimuat ulang.');
        }

        return ['ok' => 1, 'jumlah' => $n];
    }

    // Tombol silang: baris Belum -> Batal. mode 'satu' = baris ini saja,
    // mode 'sisa' = baris ini dan semua baris sesudahnya pada kode yang sama.
    // Baris yang sudah tereksekusi tidak pernah ikut berubah.
    public function spBatal(Request $req)
    {
        if (! $this->punyaHak('IsBatal')) {
            return $this->gagal('Anda tidak punya hak batal', 403);
        }

        $tanggal = $this->tanggalDariInput($req->input('tanggal'));
        if (! $tanggal) {
            return $this->gagal('Tanggal tidak valid');
        }

        $mode = $req->input('mode') === 'sisa' ? 'sisa' : 'satu';
        $operator = $mode === 'sisa' ? '>=' : '=';

        $n = DB::connection('SML')->update(
            "update DBAMORTISASIDET set Status = 2
             where kode = :kode and Tanggal {$operator} :tanggal and isnull(Status, 0) = 0", [
                'kode' => (string) $req->input('kode'),
                'tanggal' => $tanggal->format('Ymd'),
            ]);

        if ($n === 0) {
            return $this->gagal('Tidak ada jadwal Belum yang bisa dibatalkan. Data dimuat ulang.');
        }

        return ['ok' => 1, 'jumlah' => $n];
    }
}
