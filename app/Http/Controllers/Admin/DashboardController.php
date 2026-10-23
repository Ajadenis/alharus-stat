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
        $guruTerbaru = Guru::latest()->limit(3)->get();

        // ========================================
        // STATISTIK SANTRI
        // ========================================
        $totalSantri = Santri::count();
        $santriAktif = Santri::where('is_active', true)->count();
        $santriNonaktif = Santri::where('is_active', false)->count();
        $santriTerbaru = Santri::latest()->limit(3)->get();

        // ========================================
        // STATISTIK ISMA
        // ========================================
        $totalIsma = Isma::count();
        $ismaAktif = Isma::where('is_active', true)->count();
        $ismaNonaktif = Isma::where('is_active', false)->count();
        $ismaTerbaru = Isma::latest()->limit(3)->get();

        // ========================================
        // STATISTIK FASILITAS
        // ========================================
        $totalFasilitas = Fasilitas::count();
        $fasilitasAktif = Fasilitas::where('is_active', true)->count();
        $fasilitasNonaktif = Fasilitas::where('is_active', false)->count();
        $fasilitasTerbaru = Fasilitas::latest()->limit(3)->get();

        // ========================================
        // STATISTIK INFAK (SESUAIKAN DENGAN STRUKTUR DB)
        // ========================================

        $selectedYear = $request->input('infak_year', date('Y'));
        $currentYear = date('Y');
        $currentMonth = date('m');

        // 1. Total Infak Tahun Ini (pakai kolom `tahun`)
        $totalInfakTahunIni = Infak::where('tahun', $selectedYear)->sum('jumlah');

        // 2. Total Infak Bulan Ini (pakai `bulan` & `tahun`)
        $totalInfakBulanIni = Infak::where('bulan', $currentMonth)
                                    ->where('tahun', $currentYear)
                                    ->sum('jumlah');

        // 3. Total Infak Hari Ini (pakai `tanggal_bayar`)
        $totalInfakHariIni = Infak::whereDate('tanggal_bayar', date('Y-m-d'))
                                    ->sum('jumlah');

        // 4. Rata-rata Infak per Transaksi
        $rataRataInfakBulanan = Infak::where('tahun', $selectedYear)->avg('jumlah') ?? 0;

        // 5. Data Infak Per Bulan (untuk grafik bulanan)
        $namaBulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $namaBulanLengkap = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $infakPerBulan = [];
        $totalBulanan = 0;

        for ($month = 1; $month <= 12; $month++) {
                $total = Infak::where(function($q) use ($month, $namaBulan, $namaBulanLengkap) {
                $q->where('bulan', $month)                                  // 1
                  ->orWhere('bulan', $namaBulan[$month])                    // Jan
                  ->orWhere('bulan', $namaBulanLengkap[$month])             // Januari
                  ->orWhere('bulan', str_pad($month, 2, '0', STR_PAD_LEFT)); // 01
            })
            ->where('tahun', $selectedYear)
            ->sum('jumlah');

            $infakPerBulan[] = [
                'bulan' => $namaBulan[$month],
                'bulan_angka' => $month,
                'total' => (float) $total,
            ];

            $totalBulanan += $total;
        }

        // 6. Data Infak Per Tahun (5 tahun terakhir)
        $infakPerTahun = [];
        $totalTahunan = 0;

        for ($i = 4; $i >= 0; $i--) {
            $year = $currentYear - $i;
            $total = Infak::where('tahun', $year)->sum('jumlah');

            $infakPerTahun[] = [
                'tahun' => $year,
                'total' => (float) $total,
            ];

            $totalTahunan += $total;
        }

        // 7. Infak Terbaru
        $infakTerbaru = Infak::latest('tanggal_bayar')->limit(5)->get();

        // 8. Daftar Tahun untuk Filter
        $tahunList = Infak::select('tahun')
                          ->distinct()
                          ->orderBy('tahun', 'desc')
                          ->pluck('tahun');

        if (!$tahunList->contains($currentYear)) {
            $tahunList->prepend($currentYear);
        }

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
            'totalTahunan'
        ));
    }
}