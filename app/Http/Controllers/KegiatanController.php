<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kegiatan::active();

        // Pencarian
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter kategori
        if ($request->has('kategori') && !empty($request->kategori) && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Urutkan dari terbaru
        $data = $query->latest()->paginate(6)->withQueryString();

        // Daftar kategori untuk filter
        $kategoriList = Kegiatan::select('kategori')
                                ->distinct()
                                ->whereNotNull('kategori')
                                ->active()
                                ->pluck('kategori');

        // Kegiatan terbaru untuk sidebar
        $kegiatanTerbaru = Kegiatan::active()
                                   ->latest()
                                   ->limit(5)
                                   ->get();

        return view('kegiatan.index', compact('data', 'kategoriList', 'kegiatanTerbaru'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $item = Kegiatan::where('slug', $slug)
                        ->active()
                        ->firstOrFail();

        // Tambah views
        $item->increment('views');

        // Kegiatan lain untuk sidebar
        $kegiatanTerbaru = Kegiatan::active()
                                   ->where('slug', '!=', $slug)
                                   ->latest()
                                   ->limit(5)
                                   ->get();

        // Kegiatan terkait (kategori sama)
        $kegiatanTerkait = Kegiatan::where('kategori', $item->kategori)
                                   ->active()
                                   ->where('slug', '!=', $slug)
                                   ->limit(3)
                                   ->get();

        return view('kegiatan.show', compact('item', 'kegiatanTerbaru', 'kegiatanTerkait'));
    }
}