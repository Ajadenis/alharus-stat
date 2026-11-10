<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\HasFotoUrl;
use App\Models\Concerns\Activatable;

class ProgramUnggulan extends Model
{
  use HasFactory, HasFotoUrl, HasUniqueSlug, Activatable;

  protected string $fotoFolder = "program";
  protected string $fotoDefault = "default-program.png";

  protected $fillable = ["nama", "slug", "deskripsi", "foto", "icon", "kategori", "is_active"];

  protected $casts = [
    "is_active" => "boolean",
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($program) {
      $program->slug = static::buatSlugUnik($program->nama);
    });

    static::updating(function ($program) {
      if ($program->isDirty("nama")) {
        $program->slug = static::buatSlugUnik($program->nama, $program->id);
      }
    });
  }
}
