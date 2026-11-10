<?php

namespace Database\Seeders;

use App\Helpers\SantriHelper;
use Illuminate\Database\Seeder;
use App\Models\Santri;

class SantriSeeder extends Seeder
{
  public function run(): void
  {
    $santris = [
      [
        "nisa" => "1",
        "nama" => "DENIS FIRMANSYAH RAMADHAN",
        "foto" => null,
        "nis" => "00001",
        "tahun_masuk" => 2023,
        "kelas_sekarang" => SantriHelper::hitungKelas(2023),
        "jenis_kelamin" => "Laki-laki",
        "alamat" => "KP PARUNG HALANG RT03 RW01 KEC.BALEENDAH KAB.BANDUNG",
        "tanggal_lahir" => "2001-11-17",
        "nama_orang_tua" => "INEN SYAHRONI",
        "nama_wali" => "INEN SYAHRONI",
        "telepon_wali" => "088218663383",
        "status_santri" => "aktif",
        "is_active" => true,
      ],
    ];

    foreach ($santris as $santri) {
      Santri::create($santri);
    }
  }
}
