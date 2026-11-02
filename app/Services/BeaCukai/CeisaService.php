<?php

namespace App\Services\BeaCukai;

use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

// Klien API CEISA (Bea Cukai) - dipakai semua dokumen BC. Token login disimpan per user di tabel
// config('beacukai.tables.token') supaya sekali login berlaku untuk semua modul BC
// (docs/documentbc261_plan.md §7-§8). Password tidak pernah disimpan.
class CeisaService
{
    // Gateway bisa menolak request tanpa User-Agent.
    private const HEADERS = [
        'Accept' => 'application/json',
        'User-Agent' => 'Mozilla/5.0',
    ];

    /**
     * Login ke CEISA lalu simpan token untuk $userId.
     *
     * @return array [int httpStatus, array body] - body siap dikirim ke UI
     */
    public function login(string $userId, string $username, string $password): array
    {
        if (trim($username) === '' || $password === '') {
            return [400, ['message' => 'Username dan password harus diisi.']];
        }

        try {
            $res = Http::withHeaders(self::HEADERS)
                ->timeout(config('beacukai.ceisa.timeout', 30))
                ->withBody(json_encode(['username' => $username, 'password' => $password]), 'application/json')
                ->post(config('beacukai.ceisa.login_url'));
        } catch (ConnectionException $e) {
            return [500, ['message' => 'Login gagal.', 'error' => $e->getMessage()]];
        }

        if ($res->failed()) {
            return [$res->status(), ['message' => 'Login gagal.', 'error' => $res->body()]];
        }

        $json = $res->json();
        $token = is_array($json) ? self::cariNilai($json, ['access_token', 'token', 'bearerToken']) : null;
        if ($token === null) {
            return [400, ['message' => 'Token tidak ditemukan di response.']];
        }

        $expiresIn = is_array($json) ? self::cariNilai($json, ['expires_in', 'expiresIn']) : null;
        $expiresIn = is_numeric($expiresIn) ? (int) $expiresIn : (int) config('beacukai.ceisa.default_expires_in', 600);
        $expiresAt = Carbon::now('UTC')->addSeconds($expiresIn);

        $this->simpanToken($userId, (string) $token, $expiresAt, trim($username));

        return [200, ['message' => 'Login berhasil.', 'expiresAt' => $expiresAt->toIso8601String()]];
    }

    // Username CEISA dari login terakhir $userId (walau token sudah kedaluwarsa), atau null bila belum pernah login
    // sejak kolom USERNAMECEISA ada. Dikirim sebagai userPortal (BC 4.1) dan idPengguna (BC 2.5).
    public function username(string $userId): ?string
    {
        if (! $this->punyaKolomUsername()) {
            return null;
        }
        $row = DB::connection('SML')->selectOne('SELECT USERNAMECEISA FROM '.$this->tabel().' WHERE USERID = ?', [$userId]);
        $nama = $row ? trim((string) $row->USERNAMECEISA) : '';

        return $nama === '' ? null : $nama;
    }

    // Status token untuk tombol "Login BC": ['loggedIn' => bool, 'expiresAt' => ISO8601 (bila login)].
    public function status(string $userId): array
    {
        $row = $this->tokenRow($userId);
        if (! $row) {
            return ['loggedIn' => false];
        }

        return ['loggedIn' => true, 'expiresAt' => $row['expiresAt']->toIso8601String()];
    }

    // Bearer token yang masih berlaku, atau null.
    public function validToken(string $userId): ?string
    {
        $row = $this->tokenRow($userId);

        return $row ? $row['token'] : null;
    }

    /**
     * Kirim JSON dokumen yang SUDAH di-encode (urutan key harus dipertahankan, jadi tidak boleh
     * di-encode ulang oleh Guzzle).
     *
     * @param  int|null  $timeout  detik; kosong = config('beacukai.ceisa.timeout') (BC 2.3 memakai 60)
     *
     * @throws ConnectionException
     */
    public function send(string $token, string $json, ?int $timeout = null): Response
    {
        return Http::withToken($token)
            ->withHeaders(self::HEADERS)
            ->timeout($timeout ?? config('beacukai.ceisa.timeout', 30))
            ->withBody($json, 'application/json')
            ->post(config('beacukai.ceisa.document_url'));
    }

    /**
     * Ringkas pesan error CEISA menjadi maksimal 5 baris pendek (§8.8).
     *
     * @return string[]
     */
    public static function details(?string $raw): array
    {
        $raw = (string) $raw;
        $json = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [mb_substr(trim($raw), 0, 200).'...'];
        }

        $message = is_array($json) ? ($json['message'] ?? null) : null;
        if (is_string($message)) {
            $message = [$message];
        }
        if (! is_array($message) || ! array_is_list($message)) {
            return [mb_substr(json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 0, 200)];
        }

        $lines = [];
        foreach ($message as $m) {
            if (! is_string($m) || trim($m) === '') {
                continue;
            }
            $m = trim($m);
            if (stripos($m, 'lihat referensi kode error') === 0) {
                continue;
            }
            if (substr_count($m, '$') >= 2) {
                $m = substr($m, strpos($m, '$') + 1, strrpos($m, '$') - strpos($m, '$') - 1);
            } else {
                $m = preg_replace('/^\$/', '', $m);
                $m = preg_replace('/\s*\[CODE:[^\]]*\]\s*$/i', '', $m);
            }
            $lines[] = mb_substr(trim($m), 0, 200);
        }

        if (count($lines) > 5) {
            $sisa = count($lines) - 5;
            $lines = array_slice($lines, 0, 5);
            $lines[] = "+{$sisa} lagi";
        }

        return $lines;
    }

    // Cari (rekursif) nilai tidak-kosong pertama dari salah satu key.
    public static function cariNilai(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '' && ! is_array($data[$key])) {
                return $data[$key];
            }
        }
        foreach ($data as $value) {
            if (is_array($value)) {
                $found = self::cariNilai($value, $keys);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function tabel(): string
    {
        $tabel = config('beacukai.tables.token');
        if (! $tabel) {
            throw new RuntimeException('Tabel token CEISA belum dikonfigurasi (config/beacukai.php → tables.token).');
        }

        return $tabel;
    }

    // ['token' => string, 'expiresAt' => Carbon UTC] bila masih berlaku, selain itu null.
    private function tokenRow(string $userId): ?array
    {
        $row = DB::connection('SML')->selectOne(
            'SELECT BEARERTOKEN, EXPIRESAT FROM '.$this->tabel().' WHERE USERID = ?',
            [$userId]
        );
        if (! $row || ! $row->EXPIRESAT) {
            return null;
        }

        $expiresAt = Carbon::parse($row->EXPIRESAT, 'UTC');
        if ($expiresAt->lessThanOrEqualTo(Carbon::now('UTC'))) {
            return null;
        }

        return ['token' => $row->BEARERTOKEN, 'expiresAt' => $expiresAt];
    }

    // Kolom USERNAMECEISA ditambahkan script 18; sebelum script itu dijalankan, login (dipakai SEMUA modul BC)
    // tetap jalan tanpa menyimpan username.
    private function punyaKolomUsername(): bool
    {
        static $ada = [];
        $tabel = $this->tabel();
        $cari = str_starts_with($tabel, '#') ? "tempdb..{$tabel}" : $tabel; // tabel sementara (#) hanya dipakai uji

        return $ada[$tabel] ??= DB::connection('SML')->selectOne('SELECT COL_LENGTH(?, ?) AS panjang', [$cari, 'USERNAMECEISA'])->panjang !== null;
    }

    // Upsert = hapus + sisipkan dalam satu transaksi (pola OtorisasiRequestController::spSubscribe).
    private function simpanToken(string $userId, string $token, Carbon $expiresAt, string $usernameCeisa): void
    {
        $tabel = $this->tabel();
        $simpanUsername = $this->punyaKolomUsername();
        DB::connection('SML')->transaction(function () use ($tabel, $userId, $token, $expiresAt, $usernameCeisa, $simpanUsername) {
            DB::connection('SML')->delete("DELETE FROM {$tabel} WHERE USERID = ?", [$userId]);
            DB::connection('SML')->insert(
                'INSERT INTO '.$tabel.' (USERID, BEARERTOKEN, EXPIRESAT, TGLBUAT, TGLUBAH'.($simpanUsername ? ', USERNAMECEISA' : '').') VALUES (?, ?, ?, GETDATE(), GETDATE()'.($simpanUsername ? ', ?' : '').')',
                array_merge([$userId, $token, $expiresAt->format('Y-m-d H:i:s')], $simpanUsername ? [$usernameCeisa] : [])
            );
        });
    }
}
