<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Guru;
use App\Models\Santri;
use App\Models\Isma;
use App\Models\Fasilitas;
use App\Models\Infak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ========================================
        // STATISTIK KEGIATAN
        // ========================================
        $totalKegiatan = Kegiatan::count();
        $kegiatanAktif = Kegiatan::where('is_active', true)->count();
        $kegiatanNonaktif = Kegiatan::where('is_active', false)->count();
        $totalViews = Kegiatan::sum('views');
        $kegiatanTerbaru = Kegiatan::latest()->limit(5)->get();
        $kategoriList = Kegiatan::select('kategori')
                                ->distinct()
                                ->whereNotNull('kategori')
                                ->pluck('kategori');
        $totalKategori = $kategoriList->count();
        $kegiatanTahunIni = Kegiatan::whereYear('tanggal', date('Y'))->count();

        // ========================================
        // STATISTIK GURU
        // ========================================
        $totalGuru = Guru::count();
        $guruAktif = Guru::where('is_active', true)->count();
        $guruNonaktif = Guru::where('is_active', false)->count();
        $guruTerbaru = Guru::latest()->limit(5)->get();

        // ========================================
        // STATISTIK SANTRI
        // ========================================
        $totalSantri = Santri::count();
        $santriAktif = Santri::where('is_active', true)->count();
        $santriNonaktif = Santri::where('is_active', false)->count();
        $santriTerbaru = Santri::latest()->limit(5)->get();

        // ========================================
        // STATISTIK ISMA
        // ========================================
        $totalIsma = Isma::count();
        $ismaAktif = Isma::where('is_active', true)->count();
        $ismaNonaktif = Isma::where('is_active', false)->count();
        $ismaTerbaru = Isma::latest()->limit(5)->get();

        // ========================================
        // STATISTIK FASILITAS
        // ========================================
        $totalFasilitas = Fasilitas::count();
        $fasilitasAktif = Fasilitas::where('is_active', true)->count();
        $fasilitasNonaktif = Fasilitas::where('is_active', false)->count();
        $fasilitasTerbaru = Fasilitas::latest()->limit(5)->get();

        // ========================================
// STATISTIK INFAK
// ========================================
$selectedYear = $request->input('infak_year', date('Y'));
$currentYear = (int) date('Y');
$currentMonth = (int) date('m');

// Nama bulan
$namaBulan = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
    5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
    9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
];

// 1. Total Infak Tahun Ini (LUNAS saja)
$totalInfakTahunIni = Infak::where('tahun', $selectedYear)
                            ->where('status', 'lunas')
                            ->sum('jumlah');

// 2. Total Infak Bulan Ini (LUNAS saja)
$totalInfakBulanIni = Infak::where('bulan', $currentMonth)
                            ->where('tahun', $currentYear)
                            ->where('status', 'lunas')
                            ->sum('jumlah');

// 3. Total Infak Hari Ini (LUNAS saja)
$totalInfakHariIni = Infak::whereDate('tanggal_bayar', date('Y-m-d'))
                            ->where('status', 'lunas')
                            ->sum('jumlah');

// 4. Rata-rata Infak (LUNAS saja)
$rataRataInfakBulanan = Infak::where('tahun', $selectedYear)
                              ->where('status', 'lunas')
                              ->avg('jumlah') ?? 0;

// 5. Data Infak Per Bulan (LUNAS saja)
$infakPerBulan = [];
$totalBulanan = 0;

foreach ($namaBulan as $month => $nama) {
    $total = Infak::where('bulan', $month)
                  ->where('tahun', $selectedYear)
                  ->where('status', 'lunas')
                  ->sum('jumlah');

    $infakPerBulan[] = [
        'bulan' => $nama,
        'bulan_angka' => $month,
        'total' => (float) $total,
    ];

    $totalBulanan += $total;
}

// 6. Data Infak Per Tahun (LUNAS saja)
$infakPerTahun = [];
$totalTahunan = 0;

for ($i = 4; $i >= 0; $i--) {
    $year = $currentYear - $i;
    $total = Infak::where('tahun', $year)
                  ->where('status', 'lunas')
                  ->sum('jumlah');

    $infakPerTahun[] = [
        'tahun' => $year,
        'total' => (float) $total,
    ];

    $totalTahunan += $total;
}

// 7. Infak Terbaru (LUNAS saja)
$infakTerbaru = Infak::where('status', 'lunas')
                    ->latest('tanggal_bayar')
                    ->limit(5)
                    ->get();

// 8. Daftar Tahun untuk Filter
$tahunList = Infak::select('tahun')
                  ->distinct()
                  ->orderBy('tahun', 'desc')
                  ->pluck('tahun');

if (!$tahunList->contains($currentYear)) {
    $tahunList->prepend($currentYear);
}

// 9. Total Pending (untuk notifikasi)
$totalPending = Infak::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'user',
            // Kegiatan
            'totalKegiatan',
            'kegiatanAktif',
            'kegiatanNonaktif',
            'totalViews',
            'kegiatanTerbaru',
            'kategoriList',
            'totalKategori',
            'kegiatanTahunIni',
            // Guru
            'totalGuru',
            'guruAktif',
            'guruNonaktif',
            'guruTerbaru',
            // Santri
            'totalSantri',
            'santriAktif',
            'santriNonaktif',
            'santriTerbaru',
            // ISMA
            'totalIsma',
            'ismaAktif',
            'ismaNonaktif',
            'ismaTerbaru',
            // Fasilitas
            'totalFasilitas',
            'fasilitasAktif',
            'fasilitasNonaktif',
            'fasilitasTerbaru',
            // Infak
            'selectedYear',
            'totalInfakTahunIni',
            'totalInfakBulanIni',
            'totalInfakHariIni',
            'rataRataInfakBulanan',
            'infakPerBulan',
            'infakPerTahun',
            'infakTerbaru',
            'tahunList',
            'totalBulanan',
            'totalTahunan',
            'totalPending'
        ));
    }
}