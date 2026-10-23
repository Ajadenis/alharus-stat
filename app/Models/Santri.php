<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Infak;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisa',
        'nama',
        'foto',
        'nis',
        'kelas',
        'jenis_kelamin',
        'alamat',
        'tanggal_lahir',
        'nama_orang_tua',
        'nama_wali',
        'telepon_wali',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function infaks()
    {
        return $this->hasMany(Infak::class);
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('images/santri/' . $this->foto) : asset('images/default-avatar.png');
    }
}