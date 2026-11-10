<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /**
     * Buat slug dari teks. Kalau sudah dipakai, tambahkan -2, -3, dst.
     * $abaikanId dipakai saat update, supaya tidak bentrok dengan dirinya sendiri.
     */
    protected static function buatSlugUnik(string $teks, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($teks) ?: 'item';
        $slug = $dasar;
        $i = 2;

        while (static::where('slug', $slug)
            ->when($abaikanId, fn ($q) => $q->where('id', '!=', $abaikanId))
            ->exists()) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }
}
