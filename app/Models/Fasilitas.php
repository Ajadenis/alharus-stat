<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'foto',
        'icon',
        'kategori',
        'jumlah',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jumlah' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fasilitas) {
            $fasilitas->slug = Str::slug($fasilitas->nama);
        });

        static::updating(function ($fasilitas) {
            if ($fasilitas->isDirty('nama')) {
                $fasilitas->slug = Str::slug($fasilitas->nama);
            }
        });
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('images/fasilitas/' . $this->foto) : asset('images/default-fasilitas.jpg');
    }
}