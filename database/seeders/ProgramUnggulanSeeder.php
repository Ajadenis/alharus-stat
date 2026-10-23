<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgramUnggulan;

class ProgramUnggulanSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'nama' => 'Program Tahfidz Al-Quran',
                'slug' => 'program-tahfidz-al-quran',
                'deskripsi' => 'Program unggulan menghafal Al-Quran dengan metode talaqqi dan murajaah. Santri dibimbing oleh ustadz-ustadzah hafidz yang berpengalaman untuk mencapai target hafalan 30 juz dengan tartil dan tajwid yang benar.',
                'foto' => 'tahfidz.jpg',
                'icon' => 'bi-book',
                'kategori' => 'Keagamaan',
                'is_active' => true,
            ],
            [
                'nama' => 'Program Muhadhoroh',
                'slug' => 'program-muhadhoroh',
                'deskripsi' => 'Program pelatihan pidato dan public speaking dalam bahasa Arab dan Indonesia. Santri dilatih untuk berani tampil di depan umum, menyampaikan dakwah, dan menjadi pemimpin yang komunikatif.',
                'foto' => 'muhadhoroh.jpg',
                'icon' => 'bi-mic',
                'kategori' => 'Keterampilan',
                'is_active' => true,
            ],
        ];

        foreach ($programs as $program) {
            ProgramUnggulan::create($program);
        }
    }
}