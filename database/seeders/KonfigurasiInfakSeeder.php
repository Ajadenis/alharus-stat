<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KonfigurasiInfak;

class KonfigurasiInfakSeeder extends Seeder
{
    public function run(): void
    {
        KonfigurasiInfak::create([
            'nama_infak' => 'Infak Bulanan',
            'nominal' => 50000,
            'tahun' => date('Y'),
            'is_default' => true,
        ]);
    }
}