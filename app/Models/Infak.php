<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class Infak extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'nisa',
        'bulan',
        'tahun',
        'jumlah',
        'tanggal_bayar',
        'status',
        'metode_pembayaran',
        'keterangan',
        'bukti_pembayaran',
        'user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah' => 'decimal:2',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getJumlahRupiahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }
}