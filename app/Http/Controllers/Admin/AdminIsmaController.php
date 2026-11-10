<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Isma;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HandlesImages;

class AdminIsmaController extends Controller
{
  use HandlesImages;
  // INDEX
  public function index(Request $request)
  {
    $query = Isma::query();

    if ($request->has("search") && !empty($request->search)) {
      $search = $request->search;
      $query->where("nama", "LIKE", "%{$search}%");
    }

    if ($request->has("status") && !empty($request->status) && $request->status != "semua") {
      $query->where("is_active", $request->status == "aktif");
    }

    $isma = $query
      ->latest()
      ->paginate(10)
      ->withQueryString();

    return view("admin.isma.index", compact("isma"));
  }
  private function rules(): array
  {
    return [
      "nama" => "required|string|max:255",
      "jabatan" => "required|array|min:1",
      "jabatan.*" => "required|string|max:100",
      "foto" => "nullable|image|mimes:jpeg,png,jpg,webp|max:10240",
    ];
  }

  // Validasi, lalu siapkan data siap simpan (hanya field yang tervalidasi)
  private function dataTervalidasi(Request $request, ?Isma $isma = null): array
  {
    $data = $request->validate($this->rules());
    $data["is_active"] = $request->has("is_active");

    if ($request->hasFile("foto")) {
      $this->hapusGambar($isma?->foto, "isma"); // aman bila belum ada foto lama
      $data["foto"] = $this->simpanGambar($request->file("foto"), "isma", $data["nama"]);
    }

    return $data;
  }

  // CREATE
  public function create()
  {
    return view("admin.isma.create");
  }

  // STORE
  public function store(Request $request)
  {
    Isma::create($this->dataTervalidasi($request));

    return redirect()
      ->route("admin.isma.index")
      ->with("success", "Isma berhasil ditambahkan!");
  }

  // EDIT
  public function edit(int $id)
  {
    $isma = Isma::findOrFail($id);
    return view("admin.isma.edit", compact("isma"));
  }

  // UPDATE
  public function update(Request $request, int $id)
  {
    $isma = Isma::findOrFail($id);
    $isma->update($this->dataTervalidasi($request, $isma));

    return redirect()
      ->route("admin.isma.index")
      ->with("success", "isma berhasil diperbarui!");
  }

  // DESTROY
  public function destroy(int $id)
  {
    $isma = Isma::findOrFail($id);

    $this->hapusGambar($isma->foto, "isma");

    $isma->delete();

    return redirect()
      ->route("admin.isma.index")
      ->with("success", "Anggota ISMA berhasil dihapus!");
  }

  // TOGGLE STATUS
  public function toggleStatus(int $id)
  {
    $isma = Isma::findOrFail($id);
    $isma->is_active = !$isma->is_active;
    $isma->save();

    $status = $isma->is_active ? "diaktifkan" : "dinonaktifkan";
    return redirect()
      ->route("admin.isma.index")
      ->with("success", "Anggota ISMA berhasil {$status}!");
  }
}
