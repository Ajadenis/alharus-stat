<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\Activatable;

class Kegiatan extends Model
{
  use HasFactory, HasUniqueSlug, Activatable;

  protected $fillable = ["judul", "slug", "gambar", "ringkasan", "deskripsi", "kategori", "tanggal", "views", "is_active"];

  protected $casts = [
    "tanggal" => "date",
    "is_active" => "boolean",
  ];

  // Auto generate slug
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($kegiatan) {
      $kegiatan->slug = static::buatSlugUnik($kegiatan->judul);
    });

    static::updating(function ($kegiatan) {
      if ($kegiatan->isDirty("judul")) {
        $kegiatan->slug = static::buatSlugUnik($kegiatan->judul, $kegiatan->id);
      }
    });
  }

  // Scope untuk pencarian
  public function scopeSearch($query, $term)
  {
    return $query->where(function ($q) use ($term) {
      $q->where("judul", "LIKE", "%{$term}%")
        ->orWhere("ringkasan", "LIKE", "%{$term}%")
        ->orWhere("deskripsi", "LIKE", "%{$term}%");
    });
  }

  // Accessor untuk format tanggal
  public function getFormattedTanggalAttribute()
  {
    return $this->tanggal ? $this->tanggal->format("d M Y") : "-";
  }
}
