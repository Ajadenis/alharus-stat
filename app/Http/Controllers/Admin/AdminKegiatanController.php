<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminKegiatanController extends Controller
{
    // ========================================
    // ❌ HAPUS CONSTRUCTOR!
    // ========================================
    // Jangan pakai middleware di controller
    // public function __construct() { ... }

    // ========================================
    // 1. INDEX - Tampilkan semua kegiatan
    // ========================================
    public function index(Request $request)
    {
        $query = Kegiatan::query();

        // Pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('ringkasan', 'LIKE', "%{$search}%")
                  ->orWhere('kategori', 'LIKE', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->has('kategori') && !empty($request->kategori) && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Filter status
        if ($request->has('status') && !empty($request->status) && $request->status != 'semua') {
            $query->where('is_active', $request->status == 'aktif');
        }

        $kegiatan = $query->latest()->paginate(10);

        $kategoriList = Kegiatan::select('kategori')
                                ->distinct()
                                ->whereNotNull('kategori')
                                ->pluck('kategori');

        return view('admin.kegiatan.index', compact('kegiatan', 'kategoriList'));
    }

    // ========================================
    // 2. CREATE - Form tambah kegiatan
    // ========================================
    public function create()
    {
        return view('admin.kegiatan.create');
    }

    // ========================================
    // 3. STORE - Simpan kegiatan baru
    // ========================================
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'required|string',
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->judul);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
    $file = $request->file('gambar');
    $filename = time() . '_' . Str::slug($request->judul) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/kegiatan');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
$image = $manager->decodePath($file->getRealPath());
$image->scale(width: 2000, height: 2000);
$image->save($destinationPath . '/' . $filename);
    $data['gambar'] = 'kegiatan/' . $filename;
}

        Kegiatan::create($data);

        return redirect()->route('admin.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    // ========================================
    // 4. EDIT - Form edit kegiatan
    // ========================================
    public function edit(int $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        return view('admin.kegiatan.edit', compact('kegiatan'));
    }

    // ========================================
    // 5. UPDATE - Update kegiatan
    // ========================================
    public function update(Request $request,int $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'required|string',
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

if ($request->hasFile('gambar')) {
    if ($kegiatan->gambar && file_exists(public_path('images/' . $kegiatan->gambar))) {
        unlink(public_path('images/' . $kegiatan->gambar));
    }

    $file = $request->file('gambar');
    $filename = time() . '_' . Str::slug($request->judul) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/kegiatan');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
$image = $manager->decodePath($file->getRealPath());
$image->scale(width: 2000, height: 2000);
$image->save($destinationPath . '/' . $filename);

    $data['gambar'] = 'kegiatan/' . $filename;
}

        $kegiatan->update($data);

        return redirect()->route('admin.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil diperbarui!');
    }

    // ========================================
    // 6. DESTROY - Hapus kegiatan
    // ========================================
    public function destroy(int $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        if ($kegiatan->gambar && file_exists(public_path('images/' . $kegiatan->gambar))) {
            unlink(public_path('images/' . $kegiatan->gambar));
        }

        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil dihapus!');
    }

    // ========================================
    // 7. TOGGLE STATUS - Aktif/Nonaktif
    // ========================================
    public function toggleStatus(int $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->is_active = !$kegiatan->is_active;
        $kegiatan->save();

        $status = $kegiatan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.kegiatan.index')
                         ->with('success', "Kegiatan berhasil {$status}!");
    }
}