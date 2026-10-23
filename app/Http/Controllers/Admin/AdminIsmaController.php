<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Isma;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminIsmaController extends Controller
{
    // INDEX
    public function index(Request $request)
    {
        $query = Isma::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        if ($request->has('status') && !empty($request->status) && $request->status != 'semua') {
            $query->where('is_active', $request->status == 'aktif');
        }

        $isma = $query->latest()->paginate(10);

        return view('admin.isma.index', compact('isma'));
    }

    // CREATE
    public function create()
    {
        return view('admin.isma.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|array|min:1',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/isma');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        Isma::create($data);

        return redirect()->route('admin.isma.index')
                         ->with('success', 'Anggota ISMA berhasil ditambahkan!');
    }

    // EDIT
    public function edit(int $id)
    {
        $isma = Isma::findOrFail($id);
        return view('admin.isma.edit', compact('isma'));
    }

    // UPDATE
    public function update(Request $request,int  $id)
    {
        $isma = Isma::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|array|min:1',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    if ($isma->foto && file_exists(public_path('images/isma/' . $isma->foto))) {
        unlink(public_path('images/isma/' . $isma->foto));
    }

    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/isma');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        $isma->update($data);

        return redirect()->route('admin.isma.index')
                         ->with('success', 'Anggota ISMA berhasil diperbarui!');
    }

    // DESTROY
    public function destroy(int $id)
    {
        $isma = Isma::findOrFail($id);

        if ($isma->foto && file_exists(public_path('images/isma/' . $isma->foto))) {
            unlink(public_path('images/isma/' . $isma->foto));
        }

        $isma->delete();

        return redirect()->route('admin.isma.index')
                         ->with('success', 'Anggota ISMA berhasil dihapus!');
    }

    // TOGGLE STATUS
    public function toggleStatus(int $id)
    {
        $isma = Isma::findOrFail($id);
        $isma->is_active = !$isma->is_active;
        $isma->save();

        $status = $isma->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.isma.index')
                         ->with('success', "Anggota ISMA berhasil {$status}!");
    }
}