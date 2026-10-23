<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $santri_id
 * @property string $nisa
 * @property int $bulan
 * @property int $tahun
 * @property float $jumlah
 * @property \Carbon\Carbon|null $tanggal_bayar
 * @property string $status
 * @property string|null $metode_pembayaran
 * @property string|null $keterangan
 * @property string|null $bukti_pembayaran
 * @property int|null $user_id
 * @property \App\Models\Santri $santri
 * @property \App\Models\User|null $user
 * @property-read string $jumlah_rupiah
 * @property-read string $nama_bulan
 */
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
        'bulan' => 'integer',   // ✅ WAJIB: bulan = angka
        'tahun' => 'integer',   // ✅ WAJIB: tahun = angka
    ];

    // ========================================
    // RELASI
    // ========================================
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================================
    // ACCESSOR
    // ========================================
    public function getJumlahRupiahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '-';
    }

    // ========================================
    // SCOPE
    // ========================================
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }
}