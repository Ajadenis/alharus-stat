<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'foto',
        'jabatan',
        'mata_pelajaran',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jabatan' => 'array',
    ];

    // Accessor untuk URL foto
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('images/guru/' . $this->foto) : asset('images/default-avatar.png');
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