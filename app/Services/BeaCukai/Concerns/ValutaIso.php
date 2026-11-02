<?php

namespace App\Services\BeaCukai\Concerns;

use Illuminate\Container\Container;

// Kode valuta DBVALAS yang bukan ISO 4217 diterjemahkan ke kode ISO yang diminta CEISA lewat config
// beacukai.valuta_iso (RM -> MYR, EURO -> EUR). Isi DBVALAS tidak diubah; terjemahan hanya terjadi saat payload
// disusun. Kode lain dikirim apa adanya. Builder bersifat murni (boleh dipakai tanpa aplikasi Laravel), jadi
// config dibaca dari container hanya bila ada.
trait ValutaIso
{
    private function valutaIso(string $kode): string
    {
        $container = Container::getInstance();
        $peta = $container->bound('config') ? $container->make('config')->get('beacukai.valuta_iso', []) : [];
        foreach ($peta as $dari => $ke) {
            if (strcasecmp((string) $dari, $kode) === 0) {
                return (string) $ke;
            }
        }

        return $kode;
    }
}
