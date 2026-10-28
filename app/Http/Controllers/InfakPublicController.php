<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Infak;
use App\Models\KonfigurasiInfak;
use Illuminate\Http\Request;
use App\Helpers\SantriHelper;

class InfakPublicController extends Controller
{
    // ========================================
    // DAFTAR BULAN
    // ========================================
    private function getBulanList()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    // ========================================
    // INDEX
    // ========================================
    public function index()
    {
        $bulanList = $this->getBulanList();
        $konfigurasi = KonfigurasiInfak::where('is_default', true)->first();
        $nominal = $konfigurasi ? $konfigurasi->nominal : 50000;

        return view('infak.index', compact('bulanList', 'nominal'));
    }

    // ========================================
    // CARI SANTRI
    // ========================================
    public function cariSantri(Request $request)
    {
        $request->validate([
            'nisa' => 'required|string|exists:santris,nisa'
        ]);

        $santri = Santri::where('nisa', $request->nisa)->first();

        if (!$santri) {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri tidak ditemukan.'
            ], 404);
        }

        // ========================================
        // CEK STATUS SANTRI
        // ========================================
        if ($santri->status_santri === 'lulus') {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri sudah LULUS. Tidak perlu bayar infak.'
            ], 400);
        }

        if ($santri->status_santri === 'keluar') {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri sudah KELUAR. Tidak perlu bayar infak.'
            ], 400);
        }

        // ========================================
        // TENTUKAN TAHUN INFAK
        // ========================================
        $tahunSekarang = (int) date('Y');
        $tahunMasuk = (int) ($santri->tahun_masuk ?? $tahunSekarang);

        // Daftar tahun infak yang bisa dibayar
        $tahunInfak = SantriHelper::tahunInfakTersedia($tahunMasuk, $tahunSekarang);

        if (empty($tahunInfak)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada tahun infak yang tersedia.'
            ], 400);
        }

        // Tahun default: tahun sekarang (jika ada), atau tahun terakhir
        $tahunDefault = in_array($tahunSekarang, $tahunInfak)
                        ? $tahunSekarang
                        : end($tahunInfak);

        // Hitung kelas sekarang
        $kelasSekarang = SantriHelper::hitungKelas($tahunMasuk, $tahunSekarang);

        // Ambil data infak
        $infaks = $this->getInfakSantri($santri->id, $tahunDefault);

        // Konfigurasi nominal
        $konfigurasi = KonfigurasiInfak::where('is_default', true)->first();
        $nominal = $konfigurasi ? $konfigurasi->nominal : 50000;

        return response()->json([
            'status' => 'success',
            'data' => [
                'santri' => [
                    'id' => $santri->id,
                    'nisa' => $santri->nisa,
                    'nama' => $santri->nama,
                    'kelas' => $kelasSekarang,
                    'tahun_masuk' => $tahunMasuk,
                    'status_santri' => $santri->status_santri,
                    'foto' => $santri->foto_url,
                    'nama_wali' => $santri->nama_wali,
                    'telepon_wali' => $santri->telepon_wali,
                ],
                'infak' => $infaks,
                'nominal' => $nominal,
                'tahun_infak' => $tahunInfak,
                'tahun_default' => $tahunDefault,
            ]
        ]);
    }

    // ========================================
    // GET INFAK BY TAHUN
    // ========================================
    public function getInfakByTahun(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tahun' => 'required|integer',
        ]);

        $infaks = $this->getInfakSantri($request->santri_id, $request->tahun);

        return response()->json([
            'status' => 'success',
            'data' => [
                'infak' => $infaks,
                'tahun' => (int) $request->tahun,
            ]
        ]);
    }

    // ========================================
    // UPLOAD BUKTI
    // ========================================
    public function uploadBukti(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'bulan' => 'required',
            'tahun' => 'required|integer',
            'metode' => 'required|in:tunai,transfer,qris',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nama_pengirim' => 'nullable|string|max:255',
            'jumlah' => 'required|numeric|min:1',
        ]);

        // Parse bulan
        $bulanData = $request->bulan;
        if (is_string($bulanData) && str_starts_with($bulanData, '[')) {
            $bulanList = json_decode($bulanData, true);
        } elseif (is_array($bulanData)) {
            $bulanList = $bulanData;
        } else {
            $bulanList = [$bulanData];
        }

        // Konversi nama → angka
        $mapping = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        $bulanList = array_map(function ($bulan) use ($mapping) {
            if (is_numeric($bulan)) return (int) $bulan;
            return $mapping[$bulan] ?? null;
        }, $bulanList);

        $bulanList = array_values(array_filter($bulanList));

        if (empty($bulanList)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bulan yang dipilih tidak valid!'
            ], 422);
        }

        // Cek bulan lunas/pending
        $existingInfaks = Infak::where('santri_id', $request->santri_id)
                              ->where('tahun', $request->tahun)
                              ->whereIn('bulan', $bulanList)
                              ->get()
                              ->keyBy('bulan');

        $bulanBisaBayar = [];
        $bulanLunas = [];
        $bulanPending = [];

        foreach ($bulanList as $bulan) {
            if (isset($existingInfaks[$bulan])) {
                if ($existingInfaks[$bulan]->status === 'lunas') {
                    $bulanLunas[] = $bulan;
                    continue;
                }
                if ($existingInfaks[$bulan]->status === 'pending') {
                    $bulanPending[] = $bulan;
                    continue;
                }
            }
            $bulanBisaBayar[] = $bulan;
        }

        if (empty($bulanBisaBayar)) {
            $pesan = [];
            if (!empty($bulanLunas)) {
                $pesan[] = "Bulan " . implode(', ', array_map(fn($b) => $this->getBulanList()[$b], $bulanLunas)) . " sudah LUNAS";
            }
            if (!empty($bulanPending)) {
                $pesan[] = "Bulan " . implode(', ', array_map(fn($b) => $this->getBulanList()[$b], $bulanPending)) . " masih PENDING";
            }
            return response()->json([
                'status' => 'error',
                'message' => implode('. ', $pesan) . '.'
            ], 422);
        }

        // Simpan
        $santri = Santri::find($request->santri_id);
        $nominalPerBulan = $request->jumlah / count($bulanBisaBayar);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $folder = public_path('images/infak');
            if (!file_exists($folder)) mkdir($folder, 0755, true);

            $file = $request->file('bukti');
            $filename = 'infak_' . $santri->nisa . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($folder, $filename);
            $buktiPath = 'infak/' . $filename;
        }

        $successCount = 0;
        foreach ($bulanBisaBayar as $bulan) {
            $infak = Infak::firstOrNew([
                'santri_id' => $request->santri_id,
                'bulan' => $bulan,
                'tahun' => $request->tahun,
            ]);

            $infak->nisa = $santri->nisa;
            $infak->jumlah = $nominalPerBulan;
            $infak->metode_pembayaran = $request->metode;
            $infak->status = 'pending';
            $infak->bukti_pembayaran = $buktiPath;
            $infak->keterangan = 'Upload bukti oleh wali: ' . ($request->nama_pengirim ?? $santri->nama_wali);
            $infak->save();
            $successCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Bukti pembayaran untuk {$successCount} bulan berhasil diupload! Menunggu konfirmasi admin."
        ]);
    }

    // ========================================
    // GET INFAK SANTRI
    // ========================================
    private function getInfakSantri($santriId, $tahun)
    {
        $bulanList = $this->getBulanList();

        $infaks = Infak::where('santri_id', $santriId)
                      ->where('tahun', $tahun)
                      ->get()
                      ->keyBy('bulan');

        $result = [];
        foreach ($bulanList as $bulanAngka => $bulanNama) {
            $infak = $infaks->get($bulanAngka);

            $result[] = [
                'bulan' => $bulanNama,
                'bulan_angka' => $bulanAngka,
                'tahun' => (int) $tahun,
                'status' => $infak ? $infak->status : 'belum_bayar',
                'id' => $infak ? $infak->id : null,
                'jumlah' => $infak ? (float) $infak->jumlah : 0,
            ];
        }

        return $result;
    }

    public function success()
    {
        return view('infak.success');
    }
}