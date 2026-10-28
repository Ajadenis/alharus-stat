<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'foto',
        'nis',
        'nisa',
        'kelas',
        'jenis_kelamin',
        'alamat',
        'tanggal_lahir',
        'nama_orang_tua',
        'is_active',
        'tahun_masuk',
        'kelas_sekarang',
        'status_santri',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
        'tahun_masuk' => 'integer',
    ];

    // ========================================
    // ACCESSOR
    // ========================================
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('images/santri/' . $this->foto) : asset('images/default-avatar.png');
    }

    public function getUmurAttribute()
    {
        if ($this->tanggal_lahir) {
            return $this->tanggal_lahir->age . ' tahun';
        }
        return '-';
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
        return $query->where('status_santri', 'aktif');
    }

    public function scopeLulus($query)
    {
        return $query->where('status_santri', 'lulus');
    }

    public function scopeAngkatan($query, $tahun)
    {
        return $query->where('tahun_masuk', $tahun);
    }

    public function scopeKelas($query, $kelas)
    {
        return $query->where('kelas_sekarang', $kelas);
    }
}