<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasFotoUrl;
use App\Models\Concerns\HasJabatanList;
use App\Models\Concerns\Activatable;

class Isma extends Model
{
  use HasFactory, HasFotoUrl, HasJabatanList, Activatable;

  protected string $fotoFolder = "isma";
  protected string $fotoDefault = "default-avatar.png";

  protected $fillable = ["nama", "foto", "jabatan", "is_active"];

  protected $casts = [
    "is_active" => "boolean",
    "jabatan" => "array", // 👈 OTOMATIS JADI ARRAY
  ];
}
