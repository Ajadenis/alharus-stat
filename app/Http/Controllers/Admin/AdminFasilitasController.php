<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminFasilitasController extends Controller
{
    public function index(Request $request)
    {
        $query = Fasilitas::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('kategori', 'LIKE', "%{$search}%");
        }

        if ($request->has('kategori') && !empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('status') && !empty($request->status) && $request->status != 'semua') {
            $query->where('is_active', $request->status == 'aktif');
        }

        $fasilitas = $query->latest()->paginate(10);

        $kategoriList = Fasilitas::select('kategori')
                                 ->distinct()
                                 ->whereNotNull('kategori')
                                 ->pluck('kategori');

        return view('admin.fasilitas.index', compact('fasilitas', 'kategoriList'));
    }

    public function create()
    {
        return view('admin.fasilitas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'jumlah' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->nama);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/fasilitas');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        Fasilitas::create($data);

        return redirect()->route('admin.fasilitas.index')
                         ->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    public function edit(int $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        return view('admin.fasilitas.edit', compact('fasilitas'));
    }

    public function update(Request $request,int $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'jumlah' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    if ($fasilitas->foto && file_exists(public_path('images/fasilitas/' . $fasilitas->foto))) {
        unlink(public_path('images/fasilitas/' . $fasilitas->foto));
    }

    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/fasilitas');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        $fasilitas->update($data);

        return redirect()->route('admin.fasilitas.index')
                         ->with('success', 'Fasilitas berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        if ($fasilitas->foto && file_exists(public_path('images/fasilitas/' . $fasilitas->foto))) {
            unlink(public_path('images/fasilitas/' . $fasilitas->foto));
        }

        $fasilitas->delete();

        return redirect()->route('admin.fasilitas.index')
                         ->with('success', 'Fasilitas berhasil dihapus!');
    }

    public function toggleStatus(int $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->is_active = !$fasilitas->is_active;
        $fasilitas->save();

        $status = $fasilitas->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.fasilitas.index')
                         ->with('success', "Fasilitas berhasil {$status}!");
    }
}