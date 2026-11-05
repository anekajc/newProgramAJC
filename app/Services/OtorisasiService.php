<?php

namespace App\Services;

use App\Jobs\SendWebPush;
use App\Otorisasi\BatalOtorisasiHandler;
use App\Otorisasi\LeveledOtorisasiHandler;
use App\Otorisasi\OtorisasiHandler;
use Illuminate\Support\Facades\DB;

/**
 * Permintaan otorisasi lewat push (PWA). Tabel: DBREQOTORISASI, DBPUSHSUBSCRIPTION
 * (lihat database/sql/otorisasi_request.sql). Modul yang didukung: config/otorisasi.php.
 *
 * Hak otorisasi dibaca dari DBFLMENUWEB (HASACCESS + IsOtorisasi1) untuk menu yang
 * href-nya = $menuPath - sumber yang sama dengan GlobalController::getAkses().
 * Multi-level (IsOtorisasi2..5) belum didukung; LEVELOTO selalu 1.
 *
 * Dua jenis permintaan (DBREQOTORISASI.TIPE): 'O' = otorisasi (hak IsOtorisasi1), 'B' = batal
 * otorisasi (hak IsBatal; hanya untuk handler yang mengimplementasikan BatalOtorisasiHandler,
 * database/sql/otorisasi_request_batal.sql).
 */
class OtorisasiService
{
    public const OTORISASI = 'O';

    public const BATAL = 'B';

    /**
     * Kolom DBFLMENUWEB yang menjadi hak untuk jenis permintaan ini (whitelist - aman dipakai di SQL).
     * Untuk permintaan otorisasi, $level (DBREQOTORISASI.LEVELOTO, 1..5) memilih IsOtorisasi1..5.
     */
    private function kolomHak(string $tipe, int $level = 1): string
    {
        if ($tipe === self::BATAL) {
            return 'IsBatal';
        }

        return 'IsOtorisasi'.max(1, min(5, $level));
    }

    /**
     * Handler bertingkat: permintaan level N hanya untuk user yang level TERTINGGI-nya N (punya IsOtorisasiN dan tidak
     * punya level di atasnya) - mis. PO sampai 200 jt hanya untuk approver level 1, bukan direktur yang juga punya
     * level 2. Permintaan batal ikut aturan yang sama: haknya IsBatal, ditambah level tertinggi yang sesuai nilai
     * dokumen. Untuk handler satu level (Kas, Bank, PR): tidak ada pembatasan tambahan.
     */
    private function hanyaLevel(string $menuPath, string $tipe, int $level): string
    {
        if (! $this->isSupported($menuPath) || ! $this->handler($menuPath) instanceof LeveledOtorisasiHandler) {
            return '';
        }

        $level = max(1, min(5, $level));
        // Untuk 'O' IsOtorisasiN sudah diwajibkan oleh kolomHak(); untuk 'B' haknya IsBatal, jadi levelnya diwajibkan di sini.
        $sql = $tipe === self::BATAL ? ' and isnull(f.IsOtorisasi'.$level.', 0) = 1' : '';
        for ($n = $level + 1; $n <= 5; $n++) {
            $sql .= ' and isnull(f.IsOtorisasi'.$n.', 0) = 0';
        }

        return $sql;
    }

    /** Level yang menerima permintaan otorisasi dokumen ini: ditentukan handler bertingkat per dokumen, selain itu 1. */
    public function requiredLevel(string $menuPath, string $nobukti): int
    {
        $handler = $this->handler($menuPath);

        return $handler instanceof LeveledOtorisasiHandler ? $handler->approverLevel($nobukti) : 1;
    }

    public function isSupported(string $menuPath): bool
    {
        return array_key_exists($menuPath, config('otorisasi', []));
    }

    public function label(string $menuPath): string
    {
        return config("otorisasi.$menuPath.label", $menuPath);
    }

    public function handler(string $menuPath): OtorisasiHandler
    {
        return app(config("otorisasi.$menuPath.handler"));
    }

    public function kodeMenu(string $menuPath): ?string
    {
        $res = DB::connection('SML')->select('select top 1 KODEMENU from DBMENUWEB where href = :path', ['path' => $menuPath]);

        return $res ? $res[0]->KODEMENU : null;
    }

    /** Berhak otorisasi (IsOtorisasi1) menu ini? */
    public function canApprove(string $username, string $menuPath): bool
    {
        return $this->canAct($username, $menuPath, self::OTORISASI);
    }

    /** Berhak batal otorisasi (IsBatal) menu ini? Hak umum - tanpa pembatasan level (itu hanya untuk memproses satu permintaan). */
    public function canCancel(string $username, string $menuPath): bool
    {
        return $this->canAct($username, $menuPath, self::BATAL, 1, false);
    }

    /**
     * Berhak memproses permintaan jenis $tipe ('O' / 'B') di menu ini? $level = LEVELOTO permintaan.
     * $sesuaiLevel false = hanya cek hak dasarnya (IsBatal / IsOtorisasiN), tanpa aturan "level tertinggi" handler bertingkat.
     */
    public function canAct(string $username, string $menuPath, string $tipe, int $level = 1, bool $sesuaiLevel = true): bool
    {
        $res = DB::connection('SML')->select('
            select top 1 1 ok
            from DBMENUWEB m
            join DBFLMENUWEB f on f.L1 = m.KODEMENU
            where m.href = :path and f.USERID = :username and f.HASACCESS = 1 and f.'.$this->kolomHak($tipe, $level).' = 1'
            .($sesuaiLevel ? $this->hanyaLevel($menuPath, $tipe, $level) : ''), ['path' => $menuPath, 'username' => $username]);

        return (bool) $res;
    }

    /** Menu ini punya handler yang bisa membatalkan otorisasi (BatalOtorisasiHandler)? */
    public function supportsCancel(string $menuPath): bool
    {
        return $this->isSupported($menuPath) && $this->handler($menuPath) instanceof BatalOtorisasiHandler;
    }

    /**
     * Ada otorisasi yang bisa dibatalkan? Handler satu level: otorisasinya lengkap. Handler bertingkat: level mana pun sudah
     * terisi (PO di atas plafon dept head bisa baru level 1 dan tetap boleh dibatalkan).
     */
    private function adaOtorisasi(OtorisasiHandler $handler, string $nobukti): bool
    {
        return $handler instanceof LeveledOtorisasiHandler ? $handler->hasApproval($nobukti) : $handler->isApproved($nobukti);
    }

    /** USERID semua yang berhak memproses permintaan jenis $tipe (level $level) di menu ini. */
    public function approvers(string $menuPath, string $tipe = self::OTORISASI, int $level = 1): array
    {
        $res = DB::connection('SML')->select('
            select distinct f.USERID
            from DBMENUWEB m
            join DBFLMENUWEB f on f.L1 = m.KODEMENU
            where m.href = :path and f.HASACCESS = 1 and f.'.$this->kolomHak($tipe, $level).' = 1'
            .$this->hanyaLevel($menuPath, $tipe, $level), ['path' => $menuPath]);

        return array_map(fn ($r) => $r->USERID, $res);
    }

    public function find(int $id): ?object
    {
        $res = DB::connection('SML')->select('select * from DBREQOTORISASI where ID = :id', ['id' => $id]);

        return $res[0] ?? null;
    }

    /**
     * @param  string  $tipe  'O' = minta otorisasi, 'B' = minta batal otorisasi (handler harus BatalOtorisasiHandler)
     * @param  string  $alasan  alasan peminta; dipakai untuk permintaan batal
     * @return array{status: string, request: ?object}
     *                                                 status: created | exists (masih pending) | approved (sudah diotorisasi; tipe O) | noapprover | notfound
     *                                                 tipe B: notapproved (belum diotorisasi) | blocked (tidak boleh dibatalkan, alasannya di 'message') | unsupported
     *                                                 created juga membawa 'approvers' (jumlah approver) dan 'devices' (perangkat approver yang bisa dipush)
     */
    public function createRequest(string $menuPath, string $nobukti, string $username, string $tipe = self::OTORISASI, string $alasan = ''): array
    {
        $handler = $this->handler($menuPath);
        $batal = $tipe === self::BATAL;

        if ($batal && ! $handler instanceof BatalOtorisasiHandler) {
            return ['status' => 'unsupported', 'request' => null];
        }

        if (! $batal && $handler->isApproved($nobukti)) {
            return ['status' => 'approved', 'request' => null];
        }

        $kodemenu = $this->kodeMenu($menuPath);
        if (! $kodemenu || ! $handler->summary($nobukti)) {
            return ['status' => 'notfound', 'request' => null];
        }

        if ($batal) {
            if (! $this->adaOtorisasi($handler, $nobukti)) {
                return ['status' => 'notapproved', 'request' => null];
            }
            if ($blocker = $handler->cancelBlocker($nobukti)) {
                return ['status' => 'blocked', 'request' => null, 'message' => $blocker];
            }
        }

        // Tanpa approver, permintaan tidak akan pernah diproses dan tidak ada yang diberi tahu -
        // jangan disimpan (kalau disimpan, ia menggantung 'P' sampai ada yang diberi hak).
        // Handler bertingkat (PO): level penerima ditentukan handler per dokumen (mis. berdasarkan nilai PO), juga untuk batal.
        $level = $this->requiredLevel($menuPath, $nobukti);
        $approvers = array_values(array_diff($this->approvers($menuPath, $tipe, $level), [$username]));
        if (! $approvers) {
            return ['status' => 'noapprover', 'request' => null];
        }

        $existing = DB::connection('SML')->select("
            select top 1 * from DBREQOTORISASI
            where MENUPATH = :path and NOBUKTI = :nobukti and TIPE = :tipe and STATUS = 'P'
            order by ID desc
        ", ['path' => $menuPath, 'nobukti' => $nobukti, 'tipe' => $tipe]);

        if ($existing) {
            $pending = $existing[0];

            // Permintaan pending yang levelnya tidak lagi sesuai (aturan routing berubah, atau nilai dokumennya
            // berubah) dipindahkan ke level yang benar dan approver yang benar diberi tahu - kalau tidak, ia
            // menggantung di antrean orang yang tidak bisa memprosesnya.
            if ((int) $pending->LEVELOTO !== $level) {
                DB::connection('SML')->update("
                    update DBREQOTORISASI set LEVELOTO = :level where ID = :id and STATUS = 'P'
                ", ['level' => $level, 'id' => $pending->ID]);
                $pending = $this->find((int) $pending->ID);

                $this->notify($approvers, [
                    'title' => ($batal ? 'Permintaan Batal Otorisasi ' : 'Permintaan Otorisasi ').$this->label($menuPath),
                    'body' => $pending->REQUSER.($batal ? ' meminta batal otorisasi ' : ' meminta otorisasi ').$nobukti,
                    'url' => url('/otorisasi/'.$pending->ID),
                    'tag' => 'oto-'.$pending->ID,
                ]);

                return ['status' => 'exists', 'request' => $pending, 'rerouted' => count($approvers), 'devices' => $this->countDevices($approvers)];
            }

            return ['status' => 'exists', 'request' => $pending];
        }

        $inserted = DB::connection('SML')->select("
            insert into DBREQOTORISASI (KODEMENU, MENUPATH, NOBUKTI, LEVELOTO, REQUSER, TGLREQ, STATUS, REQREAD, TIPE, ALASAN)
            output inserted.ID
            values (:kodemenu, :path, :nobukti, :level, :username, getdate(), 'P', 0, :tipe, :alasan)
        ", [
            'kodemenu' => $kodemenu, 'path' => $menuPath, 'nobukti' => $nobukti, 'level' => $level, 'username' => $username,
            'tipe' => $tipe, 'alasan' => $batal ? mb_substr($alasan, 0, 255) : null,
        ]);

        $request = $this->find((int) $inserted[0]->ID);

        $this->notify($approvers, [
            'title' => ($batal ? 'Permintaan Batal Otorisasi ' : 'Permintaan Otorisasi ').$this->label($menuPath),
            'body' => $batal
                ? $username.' meminta batal otorisasi '.$nobukti.': '.$alasan
                : $username.' meminta otorisasi '.$nobukti,
            'url' => url('/otorisasi/'.$request->ID),
            'tag' => 'oto-'.$request->ID,
        ]);

        return [
            'status' => 'created',
            'request' => $request,
            'approvers' => count($approvers),
            'devices' => $this->countDevices($approvers),
        ];
    }

    /** Jumlah perangkat yang sudah berlangganan push milik USERID-USERID ini. */
    public function countDevices(array $userIds): int
    {
        if (! $userIds) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $res = DB::connection('SML')->select("select count(*) n from DBPUSHSUBSCRIPTION where USERID in ($placeholders)", array_values($userIds));

        return (int) $res[0]->n;
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function approve(int $id, string $username): array
    {
        try {
            return $this->prosesApprove($id, $username);
        } catch (\DomainException $e) {
            // Handler menolak (mis. periode terkunci): transaksinya sudah dibatalkan, permintaan tetap pending.
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @return array{ok: bool, message: string}
     */
    private function prosesApprove(int $id, string $username): array
    {
        $req = $this->find($id);
        if (! $req) {
            return ['ok' => false, 'message' => 'Permintaan tidak ditemukan'];
        }
        $tipe = $req->TIPE ?? self::OTORISASI;
        if (! $this->canAct($username, $req->MENUPATH, $tipe, (int) $req->LEVELOTO)) {
            return ['ok' => false, 'message' => $tipe === self::BATAL
                ? 'Anda tidak berhak batal otorisasi transaksi ini'
                : 'Anda tidak berhak otorisasi transaksi ini'];
        }

        $handler = $this->handler($req->MENUPATH);

        if ($tipe === self::BATAL) {
            return $this->approveBatal($req, $username, $handler);
        }

        if ($handler instanceof LeveledOtorisasiHandler) {
            return $this->approveBertingkat($req, $username, $handler);
        }

        $result = DB::connection('SML')->transaction(function () use ($req, $username, $handler) {
            // Klaim permintaan dulu - approver kedua yang menekan bersamaan akan dapat 0 baris.
            $claimed = DB::connection('SML')->update("
                update DBREQOTORISASI set STATUS = 'A', ACTUSER = :username, TGLACT = getdate(), REQREAD = 0
                where ID = :id and STATUS = 'P'
            ", ['username' => $username, 'id' => $req->ID]);

            if ($claimed === 0) {
                return ['ok' => false, 'message' => 'Permintaan ini sudah diproses'];
            }

            if ($handler->isApproved($req->NOBUKTI)) {
                // Sudah diotorisasi langsung dari halaman transaksi oleh orang lain.
                DB::connection('SML')->update("
                    update DBREQOTORISASI set STATUS = 'C', CATATAN = 'Sudah diotorisasi langsung' where ID = :id
                ", ['id' => $req->ID]);

                return ['ok' => false, 'message' => 'Transaksi sudah diotorisasi sebelumnya'];
            }

            $handler->approve($req->NOBUKTI, $username);

            return ['ok' => true, 'message' => 'Berhasil otorisasi'];
        });

        if ($result['ok']) {
            $this->notifyRequester($req, $username, 'disetujui');
        }

        return $result;
    }

    /**
     * Setujui permintaan pada handler BERTINGKAT (PO): aturan level/plafon dijalankan handler. Permintaan baru
     * ditutup ('A') kalau otorisasi sudah lengkap; kalau baru sebagian ('partial') ia tetap 'P' supaya level
     * berikutnya bisa memprosesnya; kalau ditolak aturan ('refused') tidak ada yang berubah dan tetap 'P'.
     *
     * @return array{ok: bool, message: string}
     */
    private function approveBertingkat(object $req, string $username, LeveledOtorisasiHandler&OtorisasiHandler $handler): array
    {
        $partial = false;

        $result = DB::connection('SML')->transaction(function () use ($req, $username, $handler, &$partial) {
            $current = $this->find((int) $req->ID);
            if (! $current || $current->STATUS !== 'P') {
                return ['ok' => false, 'message' => 'Permintaan ini sudah diproses'];
            }

            if ($handler->isApproved($req->NOBUKTI)) {
                // Sudah diotorisasi langsung dari halaman transaksi oleh orang lain.
                DB::connection('SML')->update("
                    update DBREQOTORISASI set STATUS = 'C', CATATAN = 'Sudah diotorisasi langsung' where ID = :id and STATUS = 'P'
                ", ['id' => $req->ID]);

                return ['ok' => false, 'message' => 'Transaksi sudah diotorisasi sebelumnya'];
            }

            $out = $handler->approveWithRules($req->NOBUKTI, $username);

            if ($out['result'] === 'refused') {
                return ['ok' => false, 'message' => $out['message']];
            }

            if ($out['result'] === 'partial') {
                $partial = true;
                DB::connection('SML')->update("
                    update DBREQOTORISASI set CATATAN = :catatan where ID = :id and STATUS = 'P'
                ", ['catatan' => mb_substr($out['message'].' (oleh '.$username.')', 0, 255), 'id' => $req->ID]);

                return ['ok' => true, 'message' => $out['message']];
            }

            DB::connection('SML')->update("
                update DBREQOTORISASI set STATUS = 'A', ACTUSER = :username, TGLACT = getdate(), REQREAD = 0
                where ID = :id and STATUS = 'P'
            ", ['username' => $username, 'id' => $req->ID]);

            return ['ok' => true, 'message' => 'Berhasil otorisasi'];
        });

        if ($result['ok']) {
            if ($partial) {
                // Peminta diberi tahu ada kemajuan; approver level tertinggi lain tahu giliran mereka.
                $this->notifyRequester($req, $username, 'disetujui sebagian: '.$result['message']);
                $others = array_values(array_diff($this->approvers($req->MENUPATH, self::OTORISASI, (int) $req->LEVELOTO), [$username]));
                $this->notify($others, [
                    'title' => 'Permintaan Otorisasi '.$this->label($req->MENUPATH),
                    'body' => $req->NOBUKTI.': '.$result['message'],
                    'url' => url('/otorisasi/'.$req->ID),
                    'tag' => 'oto-'.$req->ID,
                ]);
            } else {
                $this->notifyRequester($req, $username, 'disetujui');
            }
        }

        return $result;
    }

    /**
     * Setujui permintaan BATAL otorisasi: batalkan otorisasi dokumennya. Dicek ulang di sini (bukan hanya saat
     * permintaan dibuat) karena keadaan dokumen bisa berubah: sudah dibatalkan orang lain, atau sudah dipakai PO.
     *
     * @return array{ok: bool, message: string}
     */
    private function approveBatal(object $req, string $username, OtorisasiHandler $handler): array
    {
        if (! $handler instanceof BatalOtorisasiHandler) {
            return ['ok' => false, 'message' => 'Menu ini belum mendukung batal otorisasi'];
        }

        $result = DB::connection('SML')->transaction(function () use ($req, $username, $handler) {
            $claimed = DB::connection('SML')->update("
                update DBREQOTORISASI set STATUS = 'A', ACTUSER = :username, TGLACT = getdate(), REQREAD = 0
                where ID = :id and STATUS = 'P'
            ", ['username' => $username, 'id' => $req->ID]);

            if ($claimed === 0) {
                return ['ok' => false, 'message' => 'Permintaan ini sudah diproses'];
            }

            if (! $this->adaOtorisasi($handler, $req->NOBUKTI)) {
                DB::connection('SML')->update("
                    update DBREQOTORISASI set STATUS = 'C', CATATAN = 'Sudah dibatalkan otorisasinya' where ID = :id
                ", ['id' => $req->ID]);

                return ['ok' => false, 'message' => 'Transaksi sudah dibatalkan otorisasinya sebelumnya'];
            }

            if ($blocker = $handler->cancelBlocker($req->NOBUKTI)) {
                DB::connection('SML')->update("
                    update DBREQOTORISASI set STATUS = 'C', CATATAN = :catatan where ID = :id
                ", ['catatan' => mb_substr($blocker, 0, 255), 'id' => $req->ID]);

                return ['ok' => false, 'message' => $blocker];
            }

            $handler->cancelApproval($req->NOBUKTI, $username, (string) $req->ALASAN);

            return ['ok' => true, 'message' => 'Berhasil batal otorisasi'];
        });

        if ($result['ok']) {
            $this->notifyRequester($req, $username, 'disetujui');
        }

        return $result;
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function reject(int $id, string $username, string $catatan): array
    {
        $req = $this->find($id);
        if (! $req) {
            return ['ok' => false, 'message' => 'Permintaan tidak ditemukan'];
        }
        $tipe = $req->TIPE ?? self::OTORISASI;
        if (! $this->canAct($username, $req->MENUPATH, $tipe, (int) $req->LEVELOTO)) {
            return ['ok' => false, 'message' => $tipe === self::BATAL
                ? 'Anda tidak berhak batal otorisasi transaksi ini'
                : 'Anda tidak berhak otorisasi transaksi ini'];
        }

        $claimed = DB::connection('SML')->update("
            update DBREQOTORISASI set STATUS = 'R', ACTUSER = :username, TGLACT = getdate(), CATATAN = :catatan, REQREAD = 0
            where ID = :id and STATUS = 'P'
        ", ['username' => $username, 'catatan' => mb_substr($catatan, 0, 255), 'id' => $req->ID]);

        if ($claimed === 0) {
            return ['ok' => false, 'message' => 'Permintaan ini sudah diproses'];
        }

        $this->notifyRequester($req, $username, 'ditolak: '.$catatan);

        return ['ok' => true, 'message' => 'Permintaan ditolak'];
    }

    /**
     * Dipanggil saat transaksi diotorisasi (atau, dengan $tipe 'B', dibatalkan otorisasinya) langsung dari
     * halaman transaksinya (mis. KasController::spOtorisasi), supaya permintaan sejenis yang masih pending
     * ikut tertutup.
     */
    public function closePending(string $menuPath, string $nobukti, string $username, string $tipe = self::OTORISASI): void
    {
        $pending = DB::connection('SML')->select("
            select * from DBREQOTORISASI where MENUPATH = :path and NOBUKTI = :nobukti and TIPE = :tipe and STATUS = 'P'
        ", ['path' => $menuPath, 'nobukti' => $nobukti, 'tipe' => $tipe]);

        if (! $pending) {
            return;
        }

        DB::connection('SML')->update("
            update DBREQOTORISASI set STATUS = 'A', ACTUSER = :username, TGLACT = getdate(), REQREAD = 0
            where MENUPATH = :path and NOBUKTI = :nobukti and TIPE = :tipe and STATUS = 'P'
        ", ['username' => $username, 'path' => $menuPath, 'nobukti' => $nobukti, 'tipe' => $tipe]);

        foreach ($pending as $req) {
            $this->notifyRequester($req, $username, 'disetujui');
        }
    }

    /** Permintaan pending yang bisa diproses user ini. */
    public function pendingFor(string $username): array
    {
        $rows = DB::connection('SML')->select("
            select r.*
            from DBREQOTORISASI r
            join DBFLMENUWEB f on f.L1 = r.KODEMENU and f.USERID = :username
            where r.STATUS = 'P' and f.HASACCESS = 1
              and ((r.TIPE = 'O' and (case r.LEVELOTO when 2 then f.IsOtorisasi2 when 3 then f.IsOtorisasi3 when 4 then f.IsOtorisasi4 when 5 then f.IsOtorisasi5 else f.IsOtorisasi1 end) = 1)
                   or (r.TIPE = 'B' and f.IsBatal = 1))
            order by r.TGLREQ desc
        ", ['username' => $username]);

        // Handler bertingkat: permintaan level N hanya untuk user yang level tertingginya N (lihat hanyaLevel()).
        return array_values(array_filter($rows, fn ($r) => ! $this->isSupported($r->MENUPATH)
            || ! $this->handler($r->MENUPATH) instanceof LeveledOtorisasiHandler
            || $this->canAct($username, $r->MENUPATH, $r->TIPE ?? self::OTORISASI, (int) $r->LEVELOTO)));
    }

    /** Permintaan yang dibuat user ini (100 terakhir). */
    public function requestsBy(string $username): array
    {
        return DB::connection('SML')->select('
            select top 100 * from DBREQOTORISASI where REQUSER = :username order by TGLREQ desc
        ', ['username' => $username]);
    }

    /** Angka badge lonceng: pending untuk saya + hasil permintaan saya yang belum dibaca. */
    public function badgeCount(string $username): array
    {
        $res = DB::connection('SML')->select("
            select count(*) hasil from DBREQOTORISASI
            where REQUSER = :u and STATUS <> 'P' and REQREAD = 0
        ", ['u' => $username]);

        // pending dihitung dari pendingFor() supaya angka lonceng selalu sama dengan isi antrean "Menunggu saya".
        return ['pending' => count($this->pendingFor($username)), 'hasil' => (int) $res[0]->hasil];
    }

    public function markResultsRead(string $username): void
    {
        DB::connection('SML')->update("
            update DBREQOTORISASI set REQREAD = 1 where REQUSER = :username and STATUS <> 'P' and REQREAD = 0
        ", ['username' => $username]);
    }

    private function notifyRequester(object $req, string $actor, string $hasil): void
    {
        $prefix = ($req->TIPE ?? self::OTORISASI) === self::BATAL ? 'Batal Otorisasi ' : 'Otorisasi ';
        $this->notify([$req->REQUSER], [
            'title' => $prefix.$this->label($req->MENUPATH).' '.$req->NOBUKTI,
            'body' => 'Permintaan Anda '.$hasil.' (oleh '.$actor.')',
            'url' => url('/otorisasiinbox?tab=saya'),
            'tag' => 'oto-hasil-'.$req->ID,
        ]);
    }

    private function notify(array $userIds, array $payload): void
    {
        if ($userIds) {
            SendWebPush::dispatch($userIds, $payload);
        }
    }
}
