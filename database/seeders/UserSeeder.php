<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    foreach (config("admin.accounts") as $akun) {
      if (empty($akun["email"]) || empty($akun["password"])) {
        $this->command->warn("Akun admin dilewati: email atau password belum diisi di .env");
        continue;
      }

      User::updateOrCreate(
        ["email" => $akun["email"]],
        [
          "name" => $akun["name"],
          "password" => Hash::make($akun["password"]),
          "role" => "admin",
        ]
      );
    }
  }
}
