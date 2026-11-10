<?php

namespace App\Models\Concerns;

trait HasFotoUrl
{
  // Model yang memakai trait ini mengisi $fotoFolder (folder di public/images)
  // dan $fotoDefault (gambar pengganti bila foto kosong atau file-nya tidak ada).
  public function getFotoUrlAttribute(): string
  {
    $path = "images/" . $this->fotoFolder . "/" . $this->foto;

    if ($this->foto && file_exists(public_path($path))) {
      return asset($path);
    }

    return asset("images/" . $this->fotoDefault);
  }
}
