<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramUnggulan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'foto',
        'icon',
        'kategori',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            $program->slug = Str::slug($program->nama);
        });

        static::updating(function ($program) {
            if ($program->isDirty('nama')) {
                $program->slug = Str::slug($program->nama);
            }
        });
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('images/program/' . $this->foto) : asset('images/default-program.jpg');
    }
}