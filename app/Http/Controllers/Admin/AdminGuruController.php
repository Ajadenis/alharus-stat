<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HandlesImages;

class AdminGuruController extends Controller
{
  use HandlesImages;
  // INDEX
  public function index(Request $request)
  {
    $query = Guru::query();

    if ($request->has("search") && !empty($request->search)) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where("nama", "LIKE", "%{$search}%")
          ->orWhere("jabatan", "LIKE", "%{$search}%")
          ->orWhere("mata_pelajaran", "LIKE", "%{$search}%");
      });
    }

    if ($request->has("status") && !empty($request->status) && $request->status != "semua") {
      $query->where("is_active", $request->status == "aktif");
    }

    $guru = $query
      ->latest()
      ->paginate(10)
      ->withQueryString();

    return view("admin.guru.index", compact("guru"));
  }

  private function rules(): array
  {
    return [
      "nama" => "required|string|max:255",
      "jabatan" => "required|array|min:1",
      "jabatan.*" => "required|string|max:100",
      "mata_pelajaran" => "nullable|string|max:255",
      "foto" => "nullable|image|mimes:jpeg,png,jpg,webp|max:10240",
    ];
  }

  // Validasi, lalu siapkan data siap simpan (hanya field yang tervalidasi)
  private function dataTervalidasi(Request $request, ?Guru $guru = null): array
  {
    $data = $request->validate($this->rules());
    $data["is_active"] = $request->has("is_active");

    if ($request->hasFile("foto")) {
      $this->hapusGambar($guru?->foto, "guru"); // aman bila belum ada foto lama
      $data["foto"] = $this->simpanGambar($request->file("foto"), "guru", $data["nama"]);
    }

    return $data;
  }

  // CREATE
  public function create()
  {
    return view("admin.guru.create");
  }

  // STORE
  public function store(Request $request)
  {
    Guru::create($this->dataTervalidasi($request));

    return redirect()
      ->route("admin.guru.index")
      ->with("success", "Guru berhasil ditambahkan!");
  }

  // EDIT
  public function edit(int $id)
  {
    $guru = Guru::findOrFail($id);
    return view("admin.guru.edit", compact("guru"));
  }

  // UPDATE
  public function update(Request $request, int $id)
  {
    $guru = Guru::findOrFail($id);
    $guru->update($this->dataTervalidasi($request, $guru));

    return redirect()
      ->route("admin.guru.index")
      ->with("success", "Guru berhasil diperbarui!");
  }

  // DESTROY
  public function destroy(int $id)
  {
    $guru = Guru::findOrFail($id);

    $this->hapusGambar($guru->foto, "guru");

    $guru->delete();

    return redirect()
      ->route("admin.guru.index")
      ->with("success", "Guru berhasil dihapus!");
  }

  // TOGGLE STATUS
  public function toggleStatus(int $id)
  {
    $guru = Guru::findOrFail($id);
    $guru->is_active = !$guru->is_active;
    $guru->save();

    $status = $guru->is_active ? "diaktifkan" : "dinonaktifkan";
    return redirect()
      ->route("admin.guru.index")
      ->with("success", "Guru berhasil {$status}!");
  }
}
