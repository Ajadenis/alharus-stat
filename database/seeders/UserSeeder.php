<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya buat user admin
        User::create([
            'name' => 'Admin MDT',
            'email' => 'madrasahalharus@gmail.com',
            'password' => Hash::make('Adminalharus171101'),
            'role' => 'admin',
        ]);
        user::create([
            'name' => 'Denis Firmansyah Ramadhan',
            'email' => 'denisfirmansyah3000@gmail.com',
            'password' => hash::make('Denis011117'),
            'role' => 'admin',
        ]);
        // HAPUS user biasa
        // User::create([
        //     'name' => 'User MDT',
        //     'email' => 'user@mdtalharus.com',
        //     'password' => Hash::make('password123'),
        //     'role' => 'user',
        // ]);
    }
}