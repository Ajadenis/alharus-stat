<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\HasFotoUrl;
use App\Models\Concerns\Activatable;

class Fasilitas extends Model
{
  use HasFactory, HasFotoUrl, HasUniqueSlug, Activatable;

  protected string $fotoFolder = "fasilitas";
  protected string $fotoDefault = "default-fasilitas.png";

  protected $table = "fasilitas";

  protected $fillable = ["nama", "slug", "deskripsi", "foto", "icon", "kategori", "jumlah", "is_active"];

  protected $casts = [
    "is_active" => "boolean",
    "jumlah" => "integer",
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($fasilitas) {
      $fasilitas->slug = static::buatSlugUnik($fasilitas->nama);
    });

    static::updating(function ($fasilitas) {
      if ($fasilitas->isDirty("nama")) {
        $fasilitas->slug = static::buatSlugUnik($fasilitas->nama, $fasilitas->id);
      }
    });
  }
}
