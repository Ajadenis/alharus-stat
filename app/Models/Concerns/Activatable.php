<?php

namespace App\Models\Concerns;

trait Activatable
{
  // Hanya data yang aktif: Model::active()->...
  public function scopeActive($query)
  {
    return $query->where("is_active", true);
  }
}
