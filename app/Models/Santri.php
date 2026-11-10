<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasFotoUrl;
use App\Models\Concerns\Activatable;

class Santri extends Model
{
  use HasFactory, HasFotoUrl, Activatable;

  protected string $fotoFolder = "santri";
  protected string $fotoDefault = "default-avatar.png";

  protected $fillable = [
    "nama",
    "foto",
    "nis",
    "nisa",
    "jenis_kelamin",
    "alamat",
    "tanggal_lahir",
    "nama_orang_tua",
    "nama_wali",
    "telepon_wali",
    "is_active",
    "tahun_masuk",
    "kelas_sekarang",
    "status_santri",
  ];

  protected $casts = [
    "is_active" => "boolean",
    "tanggal_lahir" => "date",
    "tahun_masuk" => "integer",
  ];

  // Daftar kelas (dipakai validasi dan semua dropdown)
  public const KELAS = ["MDA 1", "MDA 2", "MDA 3", "MDA 4"];

  // ========================================
  // ACCESSOR
  // ========================================
  public function getUmurAttribute()
  {
    if ($this->tanggal_lahir) {
      return $this->tanggal_lahir->age . " tahun";
    }
    return "-";
  }

  // ========================================
  // RELASI
  // ========================================
  public function infaks()
  {
    return $this->hasMany(Infak::class);
  }

  // ========================================
  // SCOPE
  // ========================================
  public function scopeAktif($query)
  {
    return $query->where("status_santri", "aktif");
  }

  public function scopeLulus($query)
  {
    return $query->where("status_santri", "lulus");
  }

  public function scopeAngkatan($query, $tahun)
  {
    return $query->where("tahun_masuk", $tahun);
  }

  public function scopeKelas($query, $kelas)
  {
    return $query->where("kelas_sekarang", $kelas);
  }
}
