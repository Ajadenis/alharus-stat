<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;

class IsmaSeeder extends Seeder
{
    public function run(): void
    {
        $ismas = [
            [
                'nama' => 'Pahmi',
                'foto' => 'guru-1.jpg',
                'jabatan' => ['Kepala Madrasah', 'Guru Mapel'],
                'is_active' => true,
            ],
        ];

        foreach ($ismas as $isma) {
            Guru::create($isma);
        }
    }
}