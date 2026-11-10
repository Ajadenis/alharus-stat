<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Isma;

class IsmaSeeder extends Seeder
{
  public function run(): void
  {
    $ismas = [
      [
        "nama" => "Pahmi",
        "foto" => null,
        "jabatan" => ["Kepala Madrasah", "Guru Mapel"],
        "is_active" => true,
      ],
    ];

    foreach ($ismas as $isma) {
      Isma::create($isma);
    }
  }
}
