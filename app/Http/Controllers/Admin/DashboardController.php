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
    [$totalKegiatan, $kegiatanAktif, $kegiatanNonaktif] = $this->hitungStatus(Kegiatan::class);
    $totalViews = Kegiatan::sum("views");
    $kegiatanTerbaru = Kegiatan::latest()
      ->limit(3)
      ->get();
    $kategoriList = Kegiatan::select("kategori")
      ->distinct()
      ->whereNotNull("kategori")
      ->pluck("kategori");
    $totalKategori = $kategoriList->count();
    $kegiatanTahunIni = Kegiatan::whereYear("tanggal", date("Y"))->count();

    // ========================================
    // STATISTIK GURU
    // ========================================
    [$totalGuru, $guruAktif, $guruNonaktif] = $this->hitungStatus(Guru::class);
    $guruTerbaru = Guru::latest()
      ->limit(3)
      ->get();

    // ========================================
    // STATISTIK SANTRI
    // ========================================
    [$totalSantri, $santriAktif, $santriNonaktif] = $this->hitungStatus(Santri::class);
    $santriTerbaru = Santri::latest()
      ->limit(3)
      ->get();

    // ========================================
    // STATISTIK ISMA
    // ========================================
    [$totalIsma, $ismaAktif, $ismaNonaktif] = $this->hitungStatus(Isma::class);
    $ismaTerbaru = Isma::latest()
      ->limit(3)
      ->get();

    // ========================================
    // STATISTIK FASILITAS
    // ========================================
    [$totalFasilitas, $fasilitasAktif, $fasilitasNonaktif] = $this->hitungStatus(Fasilitas::class);
    $fasilitasTerbaru = Fasilitas::latest()
      ->limit(3)
      ->get();

    // ========================================
    // STATISTIK INFAK
    // ========================================
    $selectedYear = (int) $request->input("infak_year", date("Y"));

    // Tahun di luar rentang wajar: pakai tahun ini
    if ($selectedYear < 2000 || $selectedYear > (int) date("Y") + 1) {
      $selectedYear = (int) date("Y");
    }
    $currentYear = (int) date("Y");
    $currentMonth = (int) date("m");

    // Nama bulan
    $namaBulan = [
      1 => "Jan",
      2 => "Feb",
      3 => "Mar",
      4 => "Apr",
      5 => "Mei",
      6 => "Jun",
      7 => "Jul",
      8 => "Agu",
      9 => "Sep",
      10 => "Okt",
      11 => "Nov",
      12 => "Des",
    ];

    // 1. Total Infak Tahun Ini (LUNAS saja)
    $totalInfakTahunIni = Infak::where("tahun", $selectedYear)
      ->lunas()
      ->sum("jumlah");

    // 2. Total Infak Bulan Ini (LUNAS saja)
    $totalInfakBulanIni = Infak::where("bulan", $currentMonth)
      ->where("tahun", $currentYear)
      ->lunas()
      ->sum("jumlah");

    // 3. Total Infak Hari Ini (LUNAS saja)
    $totalInfakHariIni = Infak::whereDate("tanggal_bayar", date("Y-m-d"))
      ->lunas()
      ->sum("jumlah");

    // 4. Rata-rata Infak (LUNAS saja)
    $rataRataInfakBulanan =
      Infak::where("tahun", $selectedYear)
        ->lunas()
        ->avg("jumlah") ?? 0;

    // 5. Data Infak Per Bulan (LUNAS saja)
    $infakPerBulan = [];
    $totalBulanan = 0;

    // 1 query: total lunas per bulan untuk tahun terpilih
    $totalPerBulan = Infak::where("tahun", $selectedYear)
      ->lunas()
      ->selectRaw("bulan, SUM(jumlah) as total")
      ->groupBy("bulan")
      ->pluck("total", "bulan");

    foreach ($namaBulan as $month => $nama) {
      $total = (float) ($totalPerBulan[$month] ?? 0);

      $infakPerBulan[] = [
        "bulan" => $nama,
        "bulan_angka" => $month,
        "total" => $total,
      ];

      $totalBulanan += $total;
    }

    // 6. Data Infak Per Tahun (LUNAS saja)
    $infakPerTahun = [];
    $totalTahunan = 0;

    // 1 query: total lunas per tahun untuk 5 tahun terakhir
    $totalPerTahun = Infak::whereBetween("tahun", [$currentYear - 4, $currentYear])
      ->lunas()
      ->selectRaw("tahun, SUM(jumlah) as total")
      ->groupBy("tahun")
      ->pluck("total", "tahun");

    for ($i = 4; $i >= 0; $i--) {
      $year = $currentYear - $i;
      $total = (float) ($totalPerTahun[$year] ?? 0);

      $infakPerTahun[] = [
        "tahun" => $year,
        "total" => $total,
      ];

      $totalTahunan += $total;
    }

    // 7. Infak Terbaru (LUNAS saja)
    $infakTerbaru = Infak::lunas()
      ->latest("tanggal_bayar")
      ->limit(5)
      ->get();

    // 8. Daftar Tahun untuk Filter
    $tahunList = Infak::select("tahun")
      ->distinct()
      ->orderBy("tahun", "desc")
      ->pluck("tahun");

    if (!$tahunList->contains($currentYear)) {
      $tahunList->prepend($currentYear);
    }

    // 9. Total Pending (untuk notifikasi)
    $totalPending = Infak::where("status", "pending")->count();

    return view(
      "admin.dashboard",
      compact(
        "user",
        // Kegiatan
        "totalKegiatan",
        "kegiatanAktif",
        "kegiatanNonaktif",
        "totalViews",
        "kegiatanTerbaru",
        "kategoriList",
        "totalKategori",
        "kegiatanTahunIni",
        // Guru
        "totalGuru",
        "guruAktif",
        "guruNonaktif",
        "guruTerbaru",
        // Santri
        "totalSantri",
        "santriAktif",
        "santriNonaktif",
        "santriTerbaru",
        // ISMA
        "totalIsma",
        "ismaAktif",
        "ismaNonaktif",
        "ismaTerbaru",
        // Fasilitas
        "totalFasilitas",
        "fasilitasAktif",
        "fasilitasNonaktif",
        "fasilitasTerbaru",
        // Infak
        "selectedYear",
        "totalInfakTahunIni",
        "totalInfakBulanIni",
        "totalInfakHariIni",
        "rataRataInfakBulanan",
        "infakPerBulan",
        "infakPerTahun",
        "infakTerbaru",
        "tahunList",
        "totalBulanan",
        "totalTahunan",
        "totalPending"
      )
    );
  }
  private function hitungStatus(string $model): array
  {
    $row = $model::selectRaw("COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as aktif")->first();

    $total = (int) $row->total;
    $aktif = (int) $row->aktif;

    return [$total, $aktif, $total - $aktif];
  }
}
