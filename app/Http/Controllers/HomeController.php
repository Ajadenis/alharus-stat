<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil 3 kegiatan terbaru untuk ditampilkan di home
        $kegiatanTerbaru = Kegiatan::where('is_active', true)
                                   ->latest()
                                   ->limit(3)
                                   ->get();

        // Total kegiatan
        $totalKegiatan = Kegiatan::where('is_active', true)->count();

        // Daftar kategori
        $kategoriList = Kegiatan::select('kategori')
                                ->distinct()
                                ->whereNotNull('kategori')
                                ->where('is_active', true)
                                ->pluck('kategori');

        // Kegiatan tahun ini
        $kegiatanTahunIni = Kegiatan::where('is_active', true)
                                    ->whereYear('tanggal', date('Y'))
                                    ->count();

        return view('home.index', compact(
            'kegiatanTerbaru',
            'totalKegiatan',
            'kategoriList',
            'kegiatanTahunIni'
        ));
    }
}