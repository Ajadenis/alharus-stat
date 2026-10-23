<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Santri;

class SantriSeeder extends Seeder
{
    public function run(): void
    {
        $santris = [
            [
                'nisa' => '1',
                'nama' => 'DENIS FIRMANSYAH RAMADHAN',
                'foto' => 'contoh.jpg',
                'nis' => '00001',
                'kelas' => 'MDA 4',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'KP PARUNG HALANG RT03 RW01 KEC.BALEENDAH KAB.BANDUNG',
                'tanggal_lahir' => '2001-11-17',
                'nama_orang_tua' => 'INEN SYAHRONI',
                'nama_wali' => 'INEN SYAHRONI',
                'telepon_wali' => '088218663383',
                'status' => 'aktif',
                'is_active' => true,
            ],
        ];

        foreach ($santris as $santri) {
            Santri::create($santri);
        }
    }
}