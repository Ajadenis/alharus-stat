<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Isma extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'foto',
        'jabatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jabatan' => 'array', // 👈 OTOMATIS JADI ARRAY
    ];

    // URL Foto
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('images/isma/' . $this->foto) : asset('images/default-avatar.png');
    }

    // Tampilkan jabatan (untuk public)
    public function getJabatanListAttribute()
    {
        if (is_array($this->jabatan)) {
            return implode(' & ', $this->jabatan);
        }
        return $this->jabatan ?? '-';
    }
}