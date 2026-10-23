<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramUnggulan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminProgramUnggulanController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramUnggulan::query();

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

        $programs = $query->latest()->paginate(10);

        $kategoriList = ProgramUnggulan::select('kategori')
                                       ->distinct()
                                       ->whereNotNull('kategori')
                                       ->pluck('kategori');

        return view('admin.program.index', compact('programs', 'kategoriList'));
    }

    public function create()
    {
        return view('admin.program.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->nama);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/program');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        ProgramUnggulan::create($data);

        return redirect()->route('admin.program.index')
                         ->with('success', 'Program unggulan berhasil ditambahkan!');
    }

    public function edit(int $id)
    {
        $program = ProgramUnggulan::findOrFail($id);
        return view('admin.program.edit', compact('program'));
    }

    public function update(Request $request,int $id)
    {
        $program = ProgramUnggulan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
    if ($program->foto && file_exists(public_path('images/program/' . $program->foto))) {
        unlink(public_path('images/program/' . $program->foto));
    }

    $file = $request->file('foto');
    $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('images/program');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scale(width: 2000, height: 2000);
    $image->save($destinationPath . '/' . $filename);

    $data['foto'] = $filename;
}

        $program->update($data);

        return redirect()->route('admin.program.index')
                         ->with('success', 'Program unggulan berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $program = ProgramUnggulan::findOrFail($id);

        if ($program->foto && file_exists(public_path('images/program/' . $program->foto))) {
            unlink(public_path('images/program/' . $program->foto));
        }

        $program->delete();

        return redirect()->route('admin.program.index')
                         ->with('success', 'Program unggulan berhasil dihapus!');
    }

    public function toggleStatus(int $id)
    {
        $program = ProgramUnggulan::findOrFail($id);
        $program->is_active = !$program->is_active;
        $program->save();

        $status = $program->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.program.index')
                         ->with('success', "Program berhasil {$status}!");
    }
}