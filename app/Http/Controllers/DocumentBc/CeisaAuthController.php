<?php

namespace App\Http\Controllers\DocumentBc;

use App\Http\Controllers\Controller;
use App\Services\BeaCukai\CeisaService;
use Illuminate\Http\Request;

// Tombol "Login BC" di semua halaman dokumen BC: token CEISA per user (docs/documentbc261_plan.md §7).
class CeisaAuthController extends Controller
{
    public function __construct(private CeisaService $ceisa) {}

    // GET /bcceisastatus -> { loggedIn, expiresAt? }
    public function status()
    {
        return response()->json($this->ceisa->status(\Auth::user()->username));
    }

    // POST /bcceisalogin (username, password)
    public function login(Request $req)
    {
        [$status, $body] = $this->ceisa->login(
            \Auth::user()->username,
            (string) $req->input('username', ''),
            (string) $req->input('password', '')
        );

        return response()->json($body, $status);
    }
}
