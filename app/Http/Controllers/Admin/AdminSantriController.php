<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $request->validate([
            'nisa' => 'required|string|unique:santris|max:50',
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|unique:santris|max:50',
            'kelas' => 'required|in:MDA 1,MDA 2,MDA 3,MDA 4',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'telepon_wali' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,lulus,keluar',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->status == 'aktif';

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/santri'), $filename);
            $data['foto'] = $filename;
        }

        Santri::create($data);

        return redirect()->route('admin.santri.index')
                         ->with('success', 'Santri berhasil ditambahkan!');
    }

    public function edit(int $id)
    {
        $santri = Santri::findOrFail($id);
        return view('admin.santri.edit', compact('santri'));
    }

    public function update(Request $request,int $id)
    {
        $santri = Santri::findOrFail($id);

        $request->validate([
            'nisa' => 'required|string|max:50|unique:santris,nisa,' . $id,
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50|unique:santris,nis,' . $id,
            'kelas' => 'required|in:MDA 1,MDA 2,MDA 3,MDA 4',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'telepon_wali' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,lulus,keluar',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->status == 'aktif';

        if ($request->hasFile('foto')) {
            if ($santri->foto && file_exists(public_path('images/santri/' . $santri->foto))) {
                unlink(public_path('images/santri/' . $santri->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/santri'), $filename);
            $data['foto'] = $filename;
        }

        $santri->update($data);

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