<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HandlesImages;

class AdminFasilitasController extends Controller
{
  use HandlesImages;
  public function index(Request $request)
  {
    $query = Fasilitas::query();

    if ($request->has("search") && !empty($request->search)) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where("nama", "LIKE", "%{$search}%")->orWhere("kategori", "LIKE", "%{$search}%");
      });
    }

    if ($request->has("kategori") && !empty($request->kategori)) {
      $query->where("kategori", $request->kategori);
    }

    if ($request->has("status") && !empty($request->status) && $request->status != "semua") {
      $query->where("is_active", $request->status == "aktif");
    }

    $fasilitas = $query
      ->latest()
      ->paginate(10)
      ->withQueryString();

    $kategoriList = Fasilitas::select("kategori")
      ->distinct()
      ->whereNotNull("kategori")
      ->pluck("kategori");

    return view("admin.fasilitas.index", compact("fasilitas", "kategoriList"));
  }

  private function rules(): array
  {
    return [
      "nama" => "required|string|max:255",
      "deskripsi" => "nullable|string",
      "kategori" => "nullable|string|max:100",
      "icon" => "nullable|string|max:50",
      "jumlah" => "nullable|integer|min:0",
      "foto" => "nullable|image|mimes:jpeg,png,jpg,webp|max:10240",
    ];
  }

  // Validasi, lalu siapkan data siap simpan (hanya field yang tervalidasi)
  private function dataTervalidasi(Request $request, ?Fasilitas $fasilitas = null): array
  {
    $data = $request->validate($this->rules());
    $data["is_active"] = $request->has("is_active");

    if ($request->hasFile("foto")) {
      $this->hapusGambar($fasilitas?->foto, "fasilitas"); // aman bila belum ada foto lama
      $data["foto"] = $this->simpanGambar($request->file("foto"), "fasilitas", $data["nama"]);
    }

    return $data;
  }

  public function create()
  {
    return view("admin.fasilitas.create");
  }

  public function store(Request $request)
  {
    Fasilitas::create($this->dataTervalidasi($request));

    return redirect()
      ->route("admin.fasilitas.index")
      ->with("success", "Fasilitas berhasil ditambahkan!");
  }

  public function edit(int $id)
  {
    $fasilitas = Fasilitas::findOrFail($id);
    return view("admin.fasilitas.edit", compact("fasilitas"));
  }

  public function update(Request $request, int $id)
  {
    $fasilitas = Fasilitas::findOrFail($id);
    $fasilitas->update($this->dataTervalidasi($request, $fasilitas));

    return redirect()
      ->route("admin.fasilitas.index")
      ->with("success", "Fasilitas berhasil diperbarui!");
  }

  public function destroy(int $id)
  {
    $fasilitas = Fasilitas::findOrFail($id);

    $this->hapusGambar($fasilitas->foto, "fasilitas");

    $fasilitas->delete();

    return redirect()
      ->route("admin.fasilitas.index")
      ->with("success", "Fasilitas berhasil dihapus!");
  }

  public function toggleStatus(int $id)
  {
    $fasilitas = Fasilitas::findOrFail($id);
    $fasilitas->is_active = !$fasilitas->is_active;
    $fasilitas->save();

    $status = $fasilitas->is_active ? "diaktifkan" : "dinonaktifkan";
    return redirect()
      ->route("admin.fasilitas.index")
      ->with("success", "Fasilitas berhasil {$status}!");
  }
}
