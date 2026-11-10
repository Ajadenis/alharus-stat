<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Infak;
use App\Models\KonfigurasiInfak;
use Illuminate\Http\Request;
use App\Helpers\SantriHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class InfakPublicController extends Controller
{
  private const FORMAT_BUKTI = "jpg,jpeg,png,webp,gif,bmp,heic,heif,pdf";
  private const MAKS_BUKTI_KB = 10240; // 10 MB
  // ========================================
  // DAFTAR BULAN
  // ========================================
  private function getBulanList()
  {
    return [
      1 => "Januari",
      2 => "Februari",
      3 => "Maret",
      4 => "April",
      5 => "Mei",
      6 => "Juni",
      7 => "Juli",
      8 => "Agustus",
      9 => "September",
      10 => "Oktober",
      11 => "November",
      12 => "Desember",
    ];
  }
  // ========================================
  // NOMINAL INFAK DEFAULT
  // ========================================
  private function getNominal()
  {
    $konfigurasi = KonfigurasiInfak::where("is_default", true)->first();

    return $konfigurasi ? $konfigurasi->nominal : 50000;
  }

  // ========================================
  // INDEX
  // ========================================
  public function index()
  {
    return view("infak.index");
  }

  // ========================================
  // CARI SANTRI
  // ========================================
  public function cariSantri(Request $request)
  {
    $request->validate(
      ["nisa" => "required|string|exists:santris,nisa"],
      [
        "nisa.required" => "Masukkan NISA santri!",
        "nisa.exists" => "NISA tidak ditemukan.",
      ]
    );

    $santri = Santri::where("nisa", $request->nisa)->first();

    if (!$santri) {
      return response()->json(
        [
          "status" => "error",
          "message" => "Santri tidak ditemukan.",
        ],
        404
      );
    }

    // ========================================
    // CEK STATUS SANTRI
    // ========================================
    if ($santri->status_santri === "lulus") {
      return response()->json(
        [
          "status" => "error",
          "message" => "Santri sudah LULUS. Tidak perlu bayar infak.",
        ],
        400
      );
    }

    if ($santri->status_santri === "keluar") {
      return response()->json(
        [
          "status" => "error",
          "message" => "Santri sudah KELUAR. Tidak perlu bayar infak.",
        ],
        400
      );
    }

    // ========================================
    // TENTUKAN TAHUN INFAK
    // ========================================
    $tahunSekarang = (int) date("Y");
    $tahunMasuk = (int) ($santri->tahun_masuk ?? $tahunSekarang);

    // Daftar tahun infak yang bisa dibayar
    $tahunInfak = SantriHelper::tahunInfakTersedia($tahunMasuk, $tahunSekarang);

    if (empty($tahunInfak)) {
      return response()->json(
        [
          "status" => "error",
          "message" => "Tidak ada tahun infak yang tersedia.",
        ],
        400
      );
    }

    // Tahun default: tahun sekarang (jika ada), atau tahun terakhir
    $tahunDefault = in_array($tahunSekarang, $tahunInfak) ? $tahunSekarang : end($tahunInfak);

    // Hitung kelas sekarang
    $kelasSekarang = SantriHelper::hitungKelas($tahunMasuk);

    // Ambil data infak
    $infaks = $this->getInfakSantri($santri->id, $tahunDefault);

    // Konfigurasi nominal
    $nominal = $this->getNominal();

    session(["infak_santri_id" => $santri->id]);
    return response()->json([
      "status" => "success",
      "data" => [
        "santri" => [
          "id" => $santri->id,
          "nisa" => $santri->nisa,
          "nama" => $santri->nama,
          "kelas" => $kelasSekarang,
          "tahun_masuk" => $tahunMasuk,
          "status_santri" => $santri->status_santri,
          "foto" => $santri->foto_url,
          "nama_wali" => $santri->nama_wali,
        ],
        "infak" => $infaks,
        "nominal" => $nominal,
        "tahun_infak" => $tahunInfak,
        "tahun_default" => $tahunDefault,
      ],
    ]);
  }

  // ========================================
  // GET INFAK BY TAHUN
  // ========================================
  public function getInfakByTahun(Request $request)
  {
    $request->validate([
      "santri_id" => "required|exists:santris,id",
      "tahun" => "required|integer",
    ]);
    if ($ditolak = $this->santriTerverifikasi($request)) {
      return $ditolak;
    }

    $infaks = $this->getInfakSantri($request->santri_id, $request->tahun);

    return response()->json([
      "status" => "success",
      "data" => [
        "infak" => $infaks,
        "tahun" => (int) $request->tahun,
      ],
    ]);
  }

  // ========================================
  // UPLOAD BUKTI
  // ========================================
  public function uploadBukti(Request $request)
  {
    $request->validate(
      [
        "santri_id" => "required|integer|exists:santris,id",
        "bulan" => "required",
        "tahun" => "required|integer",
        "metode" => "required|in:tunai,transfer,qris",
        "bukti" => "required|file|mimes:" . self::FORMAT_BUKTI . "|max:" . self::MAKS_BUKTI_KB,
        "nama_pengirim" => "nullable|string|max:255",
      ],
      [
        "bukti.required" => "Upload bukti pembayaran!",
        "bukti.file" => "Bukti harus berupa file.",
        "bukti.mimes" => "Format bukti harus foto (JPG, PNG, WebP, GIF, BMP, HEIC) atau PDF.",
        "bukti.max" => "Ukuran bukti maksimal 10 MB.",
        "bukti.uploaded" => "Upload gagal. File mungkin melebihi batas yang diizinkan server.",
        "metode.in" => "Metode pembayaran tidak valid.",
      ]
    );

    if ($ditolak = $this->santriTerverifikasi($request)) {
      return $ditolak;
    }

    $santri = Santri::findOrFail($request->santri_id);

    // Hanya santri aktif yang boleh bayar
    if ($santri->status_santri !== "aktif") {
      return response()->json(["status" => "error", "message" => "Santri ini tidak berstatus aktif."], 422);
    }

    // Tahun harus salah satu tahun infak milik santri tersebut
    $tahunTersedia = SantriHelper::tahunInfakTersedia((int) ($santri->tahun_masuk ?? date("Y")));
    $tahun = (int) $request->tahun;
    if (!in_array($tahun, $tahunTersedia, true)) {
      return response()->json(["status" => "error", "message" => "Tahun infak tidak valid."], 422);
    }

    // Parse bulan (JSON string / array / nilai tunggal)
    $bulanData = $request->bulan;
    if (is_string($bulanData) && str_starts_with($bulanData, "[")) {
      $bulanList = json_decode($bulanData, true);
    } elseif (is_array($bulanData)) {
      $bulanList = $bulanData;
    } else {
      $bulanList = [$bulanData];
    }
    if (!is_array($bulanList)) {
      $bulanList = [];
    }

    // Konversi nama/angka menjadi angka 1..12
    $namaBulan = $this->getBulanList();
    $mapping = array_flip($namaBulan);

    $bulanList = array_map(function ($bulan) use ($mapping) {
      if (!is_scalar($bulan)) {
        return null;
      }
      if (is_numeric($bulan)) {
        return (int) $bulan;
      }
      return $mapping[$bulan] ?? null;
    }, $bulanList);

    $bulanList = array_values(array_unique(array_filter($bulanList, fn($b) => is_int($b) && $b >= 1 && $b <= 12)));

    if (empty($bulanList)) {
      return response()->json(["status" => "error", "message" => "Bulan yang dipilih tidak valid!"], 422);
    }

    // Pisahkan bulan yang boleh dibayar dari yang sudah lunas/pending
    $existingInfaks = Infak::where("santri_id", $santri->id)
      ->where("tahun", $tahun)
      ->whereIn("bulan", $bulanList)
      ->get()
      ->keyBy("bulan");

    $bulanBisaBayar = [];
    $bulanLunas = [];
    $bulanPending = [];

    foreach ($bulanList as $bulan) {
      $status = $existingInfaks[$bulan]->status ?? null;

      if ($status === "lunas") {
        $bulanLunas[] = $bulan;
      } elseif ($status === "pending") {
        $bulanPending[] = $bulan;
      } else {
        $bulanBisaBayar[] = $bulan;
      }
    }

    if (empty($bulanBisaBayar)) {
      $pesan = [];
      if (!empty($bulanLunas)) {
        $pesan[] = "Bulan " . implode(", ", array_map(fn($b) => $namaBulan[$b], $bulanLunas)) . " sudah LUNAS";
      }
      if (!empty($bulanPending)) {
        $pesan[] = "Bulan " . implode(", ", array_map(fn($b) => $namaBulan[$b], $bulanPending)) . " masih PENDING";
      }
      return response()->json(["status" => "error", "message" => implode(". ", $pesan) . "."], 422);
    }

    // Nominal dihitung di server, tidak percaya angka dari browser
    $nominalPerBulan = $this->getNominal();

    // Simpan file bukti
    $file = $request->file("bukti");
    $ext = strtolower($file->guessExtension());
    $filename = "infak_" . Str::slug($santri->nisa) . "_" . time() . "_" . Str::lower(Str::random(8)) . "." . $ext;

    // Simpan di penyimpanan PRIVAT (storage/app/private/infak), bukan folder publik
    $buktiPath = Storage::disk("local")->putFileAs("infak", $file, $filename);

    // Semua bulan tersimpan, atau tidak sama sekali
    try {
      DB::transaction(function () use ($bulanBisaBayar, $santri, $tahun, $nominalPerBulan, $request, $buktiPath) {
        foreach ($bulanBisaBayar as $bulan) {
          $infak = Infak::firstOrNew([
            "santri_id" => $santri->id,
            "bulan" => $bulan,
            "tahun" => $tahun,
          ]);

          $infak->nisa = $santri->nisa;
          $infak->jumlah = $nominalPerBulan;
          $infak->metode_pembayaran = $request->metode;
          $infak->status = "pending";
          $infak->bukti_pembayaran = $buktiPath;
          $infak->keterangan = "Upload bukti oleh: " . ($request->nama_pengirim ?: "tidak disebutkan");
          $infak->save();
        }
      });
    } catch (\Throwable $e) {
      Storage::disk("local")->delete($buktiPath);
      report($e);

      return response()->json(["status" => "error", "message" => "Gagal menyimpan pembayaran. Silakan coba lagi."], 500);
    }

    return response()->json([
      "status" => "success",
      "message" => "Bukti pembayaran untuk " . count($bulanBisaBayar) . " bulan berhasil diupload! Menunggu konfirmasi admin.",
    ]);
  }

  // ========================================
  // GET INFAK SANTRI
  // ========================================
  private function getInfakSantri($santriId, $tahun)
  {
    $bulanList = $this->getBulanList();

    $infaks = Infak::where("santri_id", $santriId)
      ->where("tahun", $tahun)
      ->get()
      ->keyBy("bulan");

    $result = [];
    foreach ($bulanList as $bulanAngka => $bulanNama) {
      $infak = $infaks->get($bulanAngka);

      $result[] = [
        "bulan" => $bulanNama,
        "bulan_angka" => $bulanAngka,
        "tahun" => (int) $tahun,
        "status" => $infak ? $infak->status : "belum_bayar",
        "id" => $infak ? $infak->id : null,
        "jumlah" => $infak ? (float) $infak->jumlah : 0,
      ];
    }

    return $result;
  }
  private function santriTerverifikasi(Request $request)
  {
    if ((int) $request->santri_id !== (int) session("infak_santri_id")) {
      return response()->json(["status" => "error", "message" => "Silakan cari santri berdasarkan NISA terlebih dahulu."], 403);
    }

    return null;
  }
}
