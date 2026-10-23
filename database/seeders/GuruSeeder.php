<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = [
            [
                'nama' => 'Asep Sopandi, S.Kom.i',
                'foto' => 'guru-1.jpg',
                'jabatan' => ['Kepala Madrasah','Wali Kelas'],
                'mata_pelajaran' => 'SKI',
                'is_active' => true,
            ],
        ];

        foreach ($gurus as $guru) {
            Guru::create($guru);
        }
    }
}