<?php

namespace App\Helpers;

use App\Models\Santri;
use Carbon\Carbon;

class SantriHelper
{
    /**
     * Hitung kelas berdasarkan tahun masuk & tanggal (default: hari ini).
     * Kenaikan kelas terjadi setiap tanggal 1 Juni.
     */
    public static function hitungKelas(int $tahunMasuk, ?string $tanggalSekarang = null): string
    {
        // 👇 PASTIKAN PARAMETER = $tanggalSekarang
        $tanggal = $tanggalSekarang ? Carbon::parse($tanggalSekarang) : now();

        $tahunSekarang = (int) $tanggal->format('Y');
        $bulanSekarang = (int) $tanggal->format('m');

        // 👇 LOGIKA TAHUN AJARAN (Mulai Juni)
        $tahunAjaran = $tahunSekarang;
        if ($bulanSekarang < 6) {
            $tahunAjaran = $tahunSekarang - 1;
        }

        // 👇 GUNAKAN $tahunAjaran (BUKAN $tahunSekarang)
        $selisih = $tahunAjaran - (int) $tahunMasuk;

        if ($selisih < 0) return 'Belum Masuk';
        if ($selisih == 0) return 'MDA 1';
        if ($selisih == 1) return 'MDA 2';
        if ($selisih == 2) return 'MDA 3';
        if ($selisih == 3) return 'MDA 4';
        return 'Lulus';
    }

    /**
     * Daftar tahun infak yang harus dibayar (4 tahun)
     */
    public static function tahunInfak(int $tahunMasuk): array
    {
        $tahunMasuk = (int) $tahunMasuk;
        return [
            $tahunMasuk,
            $tahunMasuk + 1,
            $tahunMasuk + 2,
            $tahunMasuk + 3,
        ];
    }

    /**
     * Tahun infak yang sudah lewat (bisa dibayar)
     */
    public static function tahunInfakTersedia(int $tahunMasuk, ?int $tahunSekarang = null): array
    {
        $tahunSekarang = $tahunSekarang ?? (int) date('Y');
        $tahunMasuk = (int) $tahunMasuk;

        $tersedia = [];
        for ($i = 0; $i < 4; $i++) {
            $tahun = $tahunMasuk + $i;
            if ($tahun <= $tahunSekarang) {
                $tersedia[] = $tahun;
            }
        }
        return $tersedia;
    }

    /**
     * Cek apakah santri sudah lulus
     */
    public static function isLulus(int $tahunMasuk, ?int $tahunSekarang = null): bool
    {
        $tahunSekarang = $tahunSekarang ?? (int) date('Y');
        return ($tahunSekarang - (int) $tahunMasuk) > 3;
    }

    /**
     * Proses update kenaikan kelas secara massal.
     * Dipanggil oleh Scheduler setiap 1 Juni.
     */
    public static function prosesKenaikanKelas()
    {
        $santris = Santri::where('status_santri', 'aktif')->get();
        $updatedCount = 0;

        foreach ($santris as $santri) {
            $kelasBaru = self::hitungKelas($santri->tahun_masuk);
            
            // Jika kelas berubah, update ke database
            if ($santri->kelas_sekarang !== $kelasBaru) {
                $santri->kelas_sekarang = $kelasBaru;
                $santri->tanggal_kenaikan_terakhir = now();

                // Jika sudah Lulus, ubah status
                if ($kelasBaru === 'Lulus') {
                    $santri->status_santri = 'lulus';
                }

                $santri->save();
                $updatedCount++;
            }
        }

        return $updatedCount;
    }
}