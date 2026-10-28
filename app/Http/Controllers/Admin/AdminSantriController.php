<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\SantriHelper;

class AdminSantriController extends Controller
{
    public function index(Request $request)
    {
        $query = Santri::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nisa', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('kelas') && !empty($request->kelas)) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->has('jenis_kelamin') && !empty($request->jenis_kelamin)) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->has('status') && !empty($request->status) && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        $santri = $query->latest()->paginate(10);
         $kelasList = ['MDA 1', 'MDA 2', 'MDA 3', 'MDA 4'];

        return view('admin.santri.index', compact('santri', 'kelasList'));
    }

    public function create()
    {
        return view('admin.santri.create');
    }

    public function store(Request $request)
{
    // ========================================
    // 1. VALIDASI
    // ========================================
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'nis' => 'required|string|unique:santris,nis',
        'nisa' => 'required|string|unique:santris,nisa',
        'tahun_masuk' => 'required|integer|min:2000|max:' . date('Y'),
        'kelas_sekarang' => 'nullable|in:MDA 1,MDA 2,MDA 3,MDA 4',
        'status_santri' => 'required|in:aktif,lulus,keluar',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'alamat' => 'nullable|string',
        'tanggal_lahir' => 'nullable|date',
        'nama_orang_tua' => 'nullable|string|max:255',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // ========================================
    // 2. DATA DASAR
    // ========================================
    $data = $request->all();
    
    // ========================================
    // 3. OTOMATIS HITUNG KELAS
    // ========================================
    // Jika kelas_sekarang KOSONG, hitung otomatis
    if (empty($data['kelas_sekarang'])) {
        $data['kelas_sekarang'] = SantriHelper::hitungKelas($request->tahun_masuk);
    }
    
    // ========================================
    // 4. STATUS AKTIF
    // ========================================
    $data['is_active'] = $request->has('is_active') ? true : false;
    
    // ========================================
    // 5. AUTO-SET STATUS_SANTRI
    // ========================================
    // Jika kelas otomatis jadi "Lulus", set status jadi lulus
    if ($data['kelas_sekarang'] === 'Lulus' && $request->status_santri === 'aktif') {
        $data['status_santri'] = 'lulus';
    }
    
    // ========================================
    // 6. UPLOAD FOTO
    // ========================================
    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
        
        // Buat folder jika belum ada
        $folder = public_path('images/santri');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        
        $file->move($folder, $filename);
        $data['foto'] = $filename;
    }
    
    // ========================================
    // 7. SIMPAN
    // ========================================
    Santri::create($data);
    
    // ========================================
    // 8. REDIRECT
    // ========================================
    return redirect()->route('admin.santri.index')
                     ->with('success', 'Santri berhasil ditambahkan!');
}

    public function edit(int $id)
    {
        $santri = Santri::findOrFail($id);
        return view('admin.santri.edit', compact('santri'));
    }

public function update(Request $request, $id)
{
    $santri = Santri::findOrFail($id);
    
    // ========================================
    // 1. VALIDASI
    // ========================================
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'nis' => 'required|string|unique:santris,nis,' . $id,
        'nisa' => 'required|string|unique:santris,nisa,' . $id,
        'tahun_masuk' => 'required|integer|min:2000|max:' . date('Y'),
        'kelas_sekarang' => 'nullable|in:MDA 1,MDA 2,MDA 3,MDA 4',
        'status_santri' => 'required|in:aktif,lulus,keluar',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'alamat' => 'nullable|string',
        'tanggal_lahir' => 'nullable|date',
        'nama_orang_tua' => 'nullable|string|max:255',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);
    
    // ========================================
    // 2. DATA DASAR
    // ========================================
    $data = $request->all();
    
    // ========================================
    // 3. OTOMATIS HITUNG KELAS
    // ========================================
    if (empty($data['kelas_sekarang'])) {
        $data['kelas_sekarang'] = SantriHelper::hitungKelas($request->tahun_masuk);
    }
    
    // ========================================
    // 4. STATUS AKTIF
    // ========================================
    $data['is_active'] = $request->has('is_active') ? true : false;
    
    // ========================================
    // 5. AUTO-SET STATUS_SANTRI
    // ========================================
    if ($data['kelas_sekarang'] === 'Lulus' && $request->status_santri === 'aktif') {
        $data['status_santri'] = 'lulus';
    }
    
    // ========================================
    // 6. UPLOAD FOTO BARU
    // ========================================
    if ($request->hasFile('foto')) {
        // Hapus foto lama
        if ($santri->foto && file_exists(public_path('images/santri/' . $santri->foto))) {
            unlink(public_path('images/santri/' . $santri->foto));
        }
        
        $file = $request->file('foto');
        $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
        
        $folder = public_path('images/santri');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        
        $file->move($folder, $filename);
        $data['foto'] = $filename;
    }
    
    // ========================================
    // 7. UPDATE
    // ========================================
    $santri->update($data);
    
    // ========================================
    // 8. REDIRECT
    // ========================================
    return redirect()->route('admin.santri.index')
                     ->with('success', 'Santri berhasil diperbarui!');
}

    public function destroy(int $id)
    {
        $santri = Santri::findOrFail($id);

        if ($santri->foto && file_exists(public_path('images/santri/' . $santri->foto))) {
            unlink(public_path('images/santri/' . $santri->foto));
        }

        $santri->delete();

        return redirect()->route('admin.santri.index')
                         ->with('success', 'Santri berhasil dihapus!');
    }

    public function toggleStatus(int $id)
    {
        $santri = Santri::findOrFail($id);
        $santri->is_active = !$santri->is_active;
        $santri->status = $santri->is_active ? 'aktif' : 'keluar';
        $santri->save();

        $status = $santri->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.santri.index')
                         ->with('success', "Santri berhasil {$status}!");
    }
}