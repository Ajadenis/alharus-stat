<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminGuruController extends Controller
{
    // INDEX
    public function index(Request $request)
    {
        $query = Guru::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('jabatan', 'LIKE', "%{$search}%")
                  ->orWhere('mata_pelajaran', 'LIKE', "%{$search}%");
        }

        if ($request->has('status') && !empty($request->status) && $request->status != 'semua') {
            $query->where('is_active', $request->status == 'aktif');
        }

        $guru = $query->latest()->paginate(10);

        return view('admin.guru.index', compact('guru'));
    }

    // CREATE
    public function create()
    {
        return view('admin.guru.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|array|min:1',
            'mata_pelajaran' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/guru');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        Guru::create($data);

        return redirect()->route('admin.guru.index')
                         ->with('success', 'Guru berhasil ditambahkan!');
    }

    // EDIT
    public function edit(int $id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    // UPDATE
    public function update(Request $request,int $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|array|min:1',
            'mata_pelajaran' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    if ($guru->foto && file_exists(public_path('images/guru/' . $guru->foto))) {
        unlink(public_path('images/guru/' . $guru->foto));
    }

    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/guru');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        $guru->update($data);

        return redirect()->route('admin.guru.index')
                         ->with('success', 'Guru berhasil diperbarui!');
    }

    // DESTROY
    public function destroy(int $id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->foto && file_exists(public_path('images/guru/' . $guru->foto))) {
            unlink(public_path('images/guru/' . $guru->foto));
        }

        $guru->delete();

        return redirect()->route('admin.guru.index')
                         ->with('success', 'Guru berhasil dihapus!');
    }

    // TOGGLE STATUS
    public function toggleStatus(int $id)
    {
        $guru = Guru::findOrFail($id);
        $guru->is_active = !$guru->is_active;
        $guru->save();

        $status = $guru->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.guru.index')
                         ->with('success', "Guru berhasil {$status}!");
    }
}