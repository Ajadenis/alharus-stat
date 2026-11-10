<?php

namespace App\Models\Concerns;

trait HasJabatanList
{
  // Tampilkan jabatan (untuk halaman publik)
  public function getJabatanListAttribute(): string
  {
    if (is_array($this->jabatan)) {
      return implode(" & ", $this->jabatan);
    }

    return $this->jabatan ?? "-";
  }
}
