<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HandlesImages;

class AdminKegiatanController extends Controller
{
  use HandlesImages;
  public function index(Request $request)
  {
    $query = Kegiatan::query();

    // Pencarian
    if ($request->has("search") && !empty($request->search)) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where("judul", "LIKE", "%{$search}%")
          ->orWhere("ringkasan", "LIKE", "%{$search}%")
          ->orWhere("kategori", "LIKE", "%{$search}%");
      });
    }

    // Filter kategori
    if ($request->has("kategori") && !empty($request->kategori) && $request->kategori != "semua") {
      $query->where("kategori", $request->kategori);
    }

    // Filter status
    if ($request->has("status") && !empty($request->status) && $request->status != "semua") {
      $query->where("is_active", $request->status == "aktif");
    }

    $kegiatan = $query
      ->latest()
      ->paginate(10)
      ->withQueryString();

    $kategoriList = Kegiatan::select("kategori")
      ->distinct()
      ->whereNotNull("kategori")
      ->pluck("kategori");

    return view("admin.kegiatan.index", compact("kegiatan", "kategoriList"));
  }

  private function rules(): array
  {
    return [
      "judul" => "required|string|max:255",
      "ringkasan" => "required|string",
      "deskripsi" => "required|string",
      "kategori" => "nullable|string|max:100",
      "tanggal" => "nullable|date",
      "gambar" => "nullable|image|mimes:jpeg,png,jpg,webp|max:10240",
    ];
  }
  private function dataTervalidasi(Request $request, ?Kegiatan $kegiatan = null): array
  {
    $data = $request->validate($this->rules());
    $data["is_active"] = $request->has("is_active");

    if ($request->hasFile("gambar")) {
      $this->hapusGambarKegiatan($kegiatan?->gambar);
      $filename = $this->simpanGambar($request->file("gambar"), "kegiatan", $data["judul"]);
      $data["gambar"] = "kegiatan/" . $filename;
    }

    return $data;
  }

  // ========================================
  // 2. CREATE - Form tambah kegiatan
  // ========================================
  public function create()
  {
    return view("admin.kegiatan.create");
  }

  // ========================================
  // 3. STORE - Simpan kegiatan baru
  // ========================================
  public function store(Request $request)
  {
    Kegiatan::create($this->dataTervalidasi($request));

    return redirect()
      ->route("admin.kegiatan.index")
      ->with("success", "Kegiatan berhasil ditambahkan!");
  }

  // ========================================
  // 4. EDIT - Form edit kegiatan
  // ========================================
  public function edit(int $id)
  {
    $kegiatan = Kegiatan::findOrFail($id);
    return view("admin.kegiatan.edit", compact("kegiatan"));
  }

  // ========================================
  // 5. UPDATE - Update kegiatan
  // ========================================
  public function update(Request $request, int $id)
  {
    $kegiatan = Kegiatan::findOrFail($id);
    $kegiatan->update($this->dataTervalidasi($request, $kegiatan));

    return redirect()
      ->route("admin.kegiatan.index")
      ->with("success", "Kegiatan berhasil diperbarui!");
  }

  // ========================================
  // 6. DESTROY - Hapus kegiatan
  // ========================================
  public function destroy(int $id)
  {
    $kegiatan = Kegiatan::findOrFail($id);

    $this->hapusGambarKegiatan($kegiatan->gambar);

    $kegiatan->delete();

    return redirect()
      ->route("admin.kegiatan.index")
      ->with("success", "Kegiatan berhasil dihapus!");
  }

  // ========================================
  // 7. TOGGLE STATUS - Aktif/Nonaktif
  // ========================================
  public function toggleStatus(int $id)
  {
    $kegiatan = Kegiatan::findOrFail($id);
    $kegiatan->is_active = !$kegiatan->is_active;
    $kegiatan->save();

    $status = $kegiatan->is_active ? "diaktifkan" : "dinonaktifkan";
    return redirect()
      ->route("admin.kegiatan.index")
      ->with("success", "Kegiatan berhasil {$status}!");
  }

  private function hapusGambarKegiatan(?string $path): void
  {
    if ($path && str_starts_with($path, "kegiatan/")) {
      $this->hapusGambar($path);
    }
  }
}
