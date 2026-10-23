<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Infak;
use App\Models\KonfigurasiInfak;
use Illuminate\Http\Request;

class InfakPublicController extends Controller
{
    public function index()
    {
        $bulanList = $this->getBulanList();
        $konfigurasi = KonfigurasiInfak::where('is_default', true)->first();
        $nominal = $konfigurasi ? $konfigurasi->nominal : 50000;

        return view('infak.index', compact('bulanList', 'nominal'));
    }

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

    // 👇 PERBAIKI METHOD INI
    public function uploadBukti(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'bulan' => 'required',
            'tahun' => 'required|integer',
            'metode' => 'required|in:tunai,transfer,qris',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nama_pengirim' => 'nullable|string|max:255',
        ]);

        // Proses data bulan (bisa array atau string)
        $bulanData = $request->bulan;
        
        // Jika bulan berupa string JSON, parse ke array
        if (is_string($bulanData) && str_starts_with($bulanData, '[')) {
            $bulanList = json_decode($bulanData, true);
        } 
        // Jika bulan berupa array langsung
        else if (is_array($bulanData)) {
            $bulanList = $bulanData;
        } 
        // Jika bulan berupa string tunggal
        else {
            $bulanList = [$bulanData];
        }

        // Pastikan bulan adalah array
        if (!is_array($bulanList)) {
            $bulanList = [$bulanList];
        }

        // Filter bulan yang valid
        $bulanValid = $this->getBulanList();
        $bulanList = array_filter($bulanList, function($bulan) use ($bulanValid) {
            return in_array($bulan, $bulanValid);
        });

        if (empty($bulanList)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bulan yang dipilih tidak valid!'
            ], 422);
        }

        $santri = Santri::find($request->santri_id);
        $tahun = $request->tahun;
        $metode = $request->metode;
        $nominalPerBulan = $request->jumlah / count($bulanList);

        // Upload bukti sekali saja
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = 'infak_' . $santri->nisa . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/infak'), $filename);
            $buktiPath = 'infak/' . $filename;
        }

        // Simpan setiap bulan
        $successCount = 0;
        foreach ($bulanList as $bulan) {
            $infak = Infak::firstOrNew([
                'santri_id' => $request->santri_id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            // Jika sudah lunas, skip
            if ($infak->status === 'lunas') {
                continue;
            }

            $infak->santri_id = $request->santri_id;
            $infak->nisa = $santri->nisa;
            $infak->bulan = $bulan;
            $infak->tahun = $tahun;
            $infak->jumlah = $nominalPerBulan;
            $infak->metode_pembayaran = $metode;
            $infak->status = 'pending';
            $infak->bukti_pembayaran = $buktiPath;
            $infak->keterangan = 'Upload bukti oleh wali: ' . ($request->nama_pengirim ?? $santri->nama_wali);
            $infak->save();
            $successCount++;
        }

        if ($successCount == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Semua bulan sudah lunas!'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Bukti pembayaran untuk {$successCount} bulan berhasil diupload! Menunggu konfirmasi admin."
        ]);
    }

    public function success()
    {
        return view('infak.success');
    }

    private function getBulanList()
    {
        return ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    }

    private function getInfakSantri($santriId)
    {
        $infaks = Infak::where('santri_id', $santriId)->get();
        $bulanList = $this->getBulanList();
        $tahun = date('Y');
        $result = [];

        foreach ($bulanList as $bulan) {
            $infak = $infaks->where('bulan', $bulan)->where('tahun', $tahun)->first();
            $result[] = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $infak ? $infak->status : 'belum_bayar',
                'id' => $infak ? $infak->id : null,
            ];
        }

        return $result;
    }
}