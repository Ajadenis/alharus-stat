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
        $query = Kegiatan::where('is_active', true);

        // Pencarian
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter kategori
        if ($request->has('kategori') && !empty($request->kategori) && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Urutkan dari terbaru
        $data = $query->latest()->paginate(6);

        // Daftar kategori untuk filter
        $kategoriList = Kegiatan::select('kategori')
                                ->distinct()
                                ->whereNotNull('kategori')
                                ->where('is_active', true)
                                ->pluck('kategori');

        // Kegiatan terbaru untuk sidebar
        $kegiatanTerbaru = Kegiatan::where('is_active', true)
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
                        ->where('is_active', true)
                        ->firstOrFail();

        // Tambah views
        $item->increment('views');

        // Kegiatan lain untuk sidebar
        $kegiatanTerbaru = Kegiatan::where('is_active', true)
                                   ->where('slug', '!=', $slug)
                                   ->latest()
                                   ->limit(5)
                                   ->get();

        // Kegiatan terkait (kategori sama)
        $kegiatanTerkait = Kegiatan::where('kategori', $item->kategori)
                                   ->where('is_active', true)
                                   ->where('slug', '!=', $slug)
                                   ->limit(3)
                                   ->get();

        return view('kegiatan.show', compact('item', 'kegiatanTerbaru', 'kegiatanTerkait'));
    }
}