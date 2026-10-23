<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'gambar',
        'ringkasan',
        'deskripsi',
        'kategori',
        'tanggal',
        'views',
        'is_active'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    // Auto generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kegiatan) {
            $kegiatan->slug = Str::slug($kegiatan->judul);
        });

        static::updating(function ($kegiatan) {
            if ($kegiatan->isDirty('judul')) {
                $kegiatan->slug = Str::slug($kegiatan->judul);
            }
        });
    }

    // Scope untuk data aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $term)
    {
        return $query->where('judul', 'LIKE', "%{$term}%")
                     ->orWhere('ringkasan', 'LIKE', "%{$term}%")
                     ->orWhere('deskripsi', 'LIKE', "%{$term}%");
    }

    // Accessor untuk format tanggal
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d M Y') : '-';
    }

    // Accessor untuk URL gambar
    public function getImageUrlAttribute()
    {
        return $this->gambar ? asset('images/' . $this->gambar) : asset('images/default-kegiatan.jpg');
    }
}