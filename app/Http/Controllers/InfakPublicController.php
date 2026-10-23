<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Infak;
use App\Models\KonfigurasiInfak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfakPublicController extends Controller
{
    // ========================================
    // DAFTAR BULAN (ANGKA → NAMA)
    // ========================================
    private function getBulanList()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
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

        $santri = Santri::where('nisa', $request->nisa)
                        ->where('is_active', true)
                        ->first();

        if (!$santri) {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri tidak ditemukan.'
            ], 404);
        }

        $infaks = $this->getInfakSantri($santri->id);
        $konfigurasi = KonfigurasiInfak::where('is_default', true)->first();
        $nominal = $konfigurasi ? $konfigurasi->nominal : 50000;

        return response()->json([
            'status' => 'success',
            'data' => [
                'santri' => [
                    'id' => $santri->id,
                    'nisa' => $santri->nisa,
                    'nama' => $santri->nama,
                    'kelas' => $santri->kelas,
                    'foto' => $santri->foto_url,
                    'nama_wali' => $santri->nama_wali,
                    'telepon_wali' => $santri->telepon_wali,
                ],
                'infak' => $infaks,
                'nominal' => $nominal,
            ]
        ]);
    }

    // ========================================
    // UPLOAD BUKTI PEMBAYARAN
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

        // ========================================
        // PARSE BULAN
        // ========================================
        $bulanData = $request->bulan;

        if (is_string($bulanData) && str_starts_with($bulanData, '[')) {
            $bulanList = json_decode($bulanData, true);
        } elseif (is_array($bulanData)) {
            $bulanList = $bulanData;
        } else {
            $bulanList = [$bulanData];
        }

        // Validasi: harus angka 1-12
        $bulanList = array_filter($bulanList, function ($bulan) {
            return is_numeric($bulan) && $bulan >= 1 && $bulan <= 12;
        });

        $bulanList = array_map('intval', $bulanList);

        if (empty($bulanList)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bulan yang dipilih tidak valid!'
            ], 422);
        }

        // ========================================
        // VALIDASI: CEK BULAN YANG SUDAH LUNAS
        // ========================================
        $existingInfaks = Infak::where('santri_id', $request->santri_id)
                              ->where('tahun', $request->tahun)
                              ->whereIn('bulan', $bulanList)
                              ->get()
                              ->keyBy('bulan');

        $bulanLunas = [];
        $bulanPending = [];
        $bulanBisaBayar = [];

        foreach ($bulanList as $bulan) {
            if (isset($existingInfaks[$bulan])) {
                if ($existingInfaks[$bulan]->status === 'lunas') {
                    $bulanLunas[] = $bulan;
                } elseif ($existingInfaks[$bulan]->status === 'pending') {
                    $bulanPending[] = $bulan;
                } else {
                    $bulanBisaBayar[] = $bulan;
                }
            } else {
                $bulanBisaBayar[] = $bulan;
            }
        }

        // Jika semua bulan sudah lunas
        if (empty($bulanBisaBayar)) {
            $pesan = [];
            if (!empty($bulanLunas)) {
                $pesan[] = "Bulan " . implode(', ', array_map(fn($b) => $this->getBulanList()[$b], $bulanLunas)) . " sudah LUNAS";
            }
            if (!empty($bulanPending)) {
                $pesan[] = "Bulan " . implode(', ', array_map(fn($b) => $this->getBulanList()[$b], $bulanPending)) . " masih PENDING (menunggu konfirmasi admin)";
            }

            return response()->json([
                'status' => 'error',
                'message' => implode('. ', $pesan) . '.'
            ], 422);
        }

        // ========================================
        // UPLOAD BUKTI
        // ========================================
        $santri = Santri::find($request->santri_id);
        $tahun = $request->tahun;
        $metode = $request->metode;
        $nominalPerBulan = $request->jumlah / count($bulanBisaBayar);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $folder = public_path('images/infak');
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $file = $request->file('bukti');
            $filename = 'infak_' . $santri->nisa . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($folder, $filename);
            $buktiPath = 'infak/' . $filename;
        }

        // ========================================
        // SIMPAN DATA
        // ========================================
        $successCount = 0;

        foreach ($bulanBisaBayar as $bulan) {
            $infak = Infak::firstOrNew([
                'santri_id' => $request->santri_id,
                'bulan' => $bulan,   // 👈 angka
                'tahun' => $tahun,
            ]);

            $infak->nisa = $santri->nisa;
            $infak->jumlah = $nominalPerBulan;
            $infak->metode_pembayaran = $metode;
            $infak->status = 'pending';
            $infak->bukti_pembayaran = $buktiPath;
            $infak->keterangan = 'Upload bukti oleh wali: ' . ($request->nama_pengirim ?? $santri->nama_wali);
            $infak->save();
            $successCount++;
        }

        // ========================================
        // RESPONSE
        // ========================================
        $message = "Bukti pembayaran untuk {$successCount} bulan berhasil diupload! Menunggu konfirmasi admin.";

        if (!empty($bulanLunas)) {
            $message .= " (Bulan " . implode(', ', array_map(fn($b) => $this->getBulanList()[$b], $bulanLunas)) . " dilewati karena sudah lunas)";
        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    // ========================================
    // SUCCESS PAGE
    // ========================================
    public function success()
    {
        return view('infak.success');
    }

    // ========================================
    // GET INFAK SANTRI (PER BULAN)
    // ========================================
    private function getInfakSantri($santriId)
    {
        $tahun = date('Y');
        $bulanList = $this->getBulanList();

        // Ambil semua infak santri di tahun ini
        $infaks = Infak::where('santri_id', $santriId)
                      ->where('tahun', $tahun)
                      ->get()
                      ->keyBy('bulan');   // key = bulan (angka)

        $result = [];

        foreach ($bulanList as $bulanAngka => $bulanNama) {
            $infak = $infaks->get($bulanAngka);

            $result[] = [
                'bulan' => $bulanNama,
                'bulan_angka' => $bulanAngka,
                'tahun' => $tahun,
                'status' => $infak ? $infak->status : 'belum_bayar',
                'id' => $infak ? $infak->id : null,
                'jumlah' => $infak ? $infak->jumlah : 0,
                'tanggal_bayar' => $infak && $infak->tanggal_bayar
                                    ? $infak->tanggal_bayar->format('d M Y')
                                    : null,
            ];
        }

        return $result;
    }
}