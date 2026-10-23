<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infak;
use Illuminate\Http\Request;

class AdminInfakController extends Controller
{
    // INDEX
    public function index(Request $request)
    {
        $query = Infak::with('santri')->orderBy('created_at', 'desc');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('nisa') && !empty($request->nisa)) {
            $query->where('nisa', 'LIKE', "%{$request->nisa}%");
        }

        $infaks = $query->paginate(20);

        return view('admin.infak.index', compact('infaks'));
    }

    // SHOW
    public function show(int $id)
    {
        $infak = Infak::with('santri', 'user')->findOrFail($id);
        return view('admin.infak.show', compact('infak'));
    }

    // KONFIRMASI
    public function konfirmasi(int $id)
    {
        $infak = Infak::findOrFail($id);
        $infak->status = 'lunas';
        $infak->tanggal_bayar = now();
        $infak->user_id = auth()->id();
        $infak->save();

        return redirect()->route('admin.infak.index')
                         ->with('success', "✅ Pembayaran {$infak->bulan} {$infak->tahun} (NISA: {$infak->nisa}) berhasil dikonfirmasi!");
    }

    // TOLAK
    public function tolak(int $id)
    {
        $infak = Infak::findOrFail($id);
        $infak->status = 'belum_bayar';
        $infak->keterangan = 'Ditolak oleh admin: ' . auth()->user()->name;
        $infak->save();

        return redirect()->route('admin.infak.index')
                         ->with('warning', "❌ Pembayaran {$infak->bulan} {$infak->tahun} (NISA: {$infak->nisa}) ditolak!");
    }

    // 👇 ALL ACCEPT - KONFIRMASI SEMUA PENDING
    public function allAccept()
    {
        $infaks = Infak::where('status', 'pending')->get();
        $count = $infaks->count();

        if ($count == 0) {
            return redirect()->route('admin.infak.index')
                             ->with('warning', 'Tidak ada pembayaran pending yang perlu dikonfirmasi.');
        }

        foreach ($infaks as $infak) {
            $infak->status = 'lunas';
            $infak->tanggal_bayar = now();
            $infak->user_id = auth()->id();
            $infak->save();
        }

        return redirect()->route('admin.infak.index')
                         ->with('success', "✅ Semua pembayaran pending ({$count} data) berhasil dikonfirmasi!");
    }

    // DESTROY
    public function destroy(int $id)
    {
        $infak = Infak::findOrFail($id);

        if ($infak->bukti_pembayaran && file_exists(public_path('images/' . $infak->bukti_pembayaran))) {
            unlink(public_path('images/' . $infak->bukti_pembayaran));
        }

        $infak->delete();

        return redirect()->route('admin.infak.index')
                         ->with('success', 'Data infak berhasil dihapus!');
    }
}