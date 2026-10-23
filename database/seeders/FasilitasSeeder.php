<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fasilitas;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $fasilitas = [
            [
                'nama' => 'Masjid Al-Harus',
                'deskripsi' => 'Masjid sebagai pusat ibadah dan kegiatan keagamaan santri. Kapasitas 500 jamaah dengan fasilitas AC, sound system, dan ruang wudhu yang memadai.',
                'foto' => 'masjid.jpg',
                'icon' => 'bi-building',
                'kategori' => 'Ibadah',
                'jumlah' => 1,
                'is_active' => true,
            ],
            [
                'nama' => 'Koperasi Santri',
                'deskripsi' => 'Perpustakaan dengan koleksi ribuan buku, kitab kuning, dan referensi ilmiah. Dilengkapi ruang baca yang nyaman dan area digital.',
                'foto' => 'perpustakaan.jpg',
                'icon' => 'bi-book',
                'kategori' => 'Akademik',
                'jumlah' => 2,
                'is_active' => true,
            ],
            [
                'nama' => 'Ruang Guru',
                'deskripsi' => 'Asrama yang nyaman untuk Guru dengan kapasitas 200 santri. Dilengkapi kamar tidur, ruang belajar, dan fasilitas kebersihan.',
                'foto' => 'asrama.jpg',
                'icon' => 'bi-house',
                'kategori' => 'Akomodasi',
                'jumlah' => 200,
                'is_active' => true,
            ],
            [
                'nama' => 'Aula Serbaguna',
                'deskripsi' => 'Aula serbaguna untuk acara besar, seminar, dan kegiatan santri. Kapasitas 300 orang dengan sound system dan layar proyektor.',
                'foto' => 'aula.jpg',
                'icon' => 'bi-easel',
                'kategori' => 'Fasilitas',
                'jumlah' => 1,
                'is_active' => true,
            ],
            [
                'nama' => 'Lapangan Olahraga',
                'deskripsi' => 'Lapangan olahraga multifungsi untuk futsal, basket, dan voli. Dilengkapi dengan lampu penerangan untuk kegiatan malam.',
                'foto' => 'lapangan.jpg',
                'icon' => 'bi-controller',
                'kategori' => 'Olahraga',
                'jumlah' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($fasilitas as $item) {
            Fasilitas::create($item);
        }
    }
}