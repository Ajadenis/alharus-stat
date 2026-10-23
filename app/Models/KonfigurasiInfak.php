<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfigurasiInfak extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_infak',
        'nominal',
        'tahun',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'nominal' => 'decimal:2',
    ];

    public function getNominalRupiahAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}