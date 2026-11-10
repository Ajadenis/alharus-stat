<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $this->call([
      UserSeeder::class,
      GuruSeeder::class,
      IsmaSeeder::class,
      FasilitasSeeder::class,
      ProgramUnggulanSeeder::class,
      KegiatanSeeder::class,
      KonfigurasiInfakSeeder::class,
      SantriSeeder::class,
    ]);
  }
}
