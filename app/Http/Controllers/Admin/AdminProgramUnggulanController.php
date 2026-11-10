<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramUnggulan;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HandlesImages;

class AdminProgramUnggulanController extends Controller
{
  use HandlesImages;
  public function index(Request $request)
  {
    $query = ProgramUnggulan::query();

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

    $programs = $query
      ->latest()
      ->paginate(10)
      ->withQueryString();

    $kategoriList = ProgramUnggulan::select("kategori")
      ->distinct()
      ->whereNotNull("kategori")
      ->pluck("kategori");

    return view("admin.program.index", compact("programs", "kategoriList"));
  }

  private function rules(): array
  {
    return [
      "nama" => "required|string|max:255",
      "deskripsi" => "required|string",
      "kategori" => "nullable|string|max:100",
      "icon" => "nullable|string|max:50",
      "foto" => "nullable|image|mimes:jpeg,png,jpg,webp|max:10240",
    ];
  }

  // Validasi, lalu siapkan data siap simpan (hanya field yang tervalidasi)
  private function dataTervalidasi(Request $request, ?ProgramUnggulan $program = null): array
  {
    $data = $request->validate($this->rules());
    $data["is_active"] = $request->has("is_active");

    if ($request->hasFile("foto")) {
      $this->hapusGambar($program?->foto, "program"); // aman bila belum ada foto lama
      $data["foto"] = $this->simpanGambar($request->file("foto"), "program", $data["nama"]);
    }

    return $data;
  }

  public function create()
  {
    return view("admin.program.create");
  }

  public function store(Request $request)
  {
    ProgramUnggulan::create($this->dataTervalidasi($request));

    return redirect()
      ->route("admin.program.index")
      ->with("success", "Program berhasil ditambahkan!");
  }

  public function edit(int $id)
  {
    $program = ProgramUnggulan::findOrFail($id);
    return view("admin.program.edit", compact("program"));
  }

  public function update(Request $request, int $id)
  {
    $program = ProgramUnggulan::findOrFail($id);
    $program->update($this->dataTervalidasi($request, $program));

    return redirect()
      ->route("admin.program.index")
      ->with("success", "Program berhasil diperbarui!");
  }

  public function destroy(int $id)
  {
    $program = ProgramUnggulan::findOrFail($id);

    $this->hapusGambar($program->foto, "program");

    $program->delete();

    return redirect()
      ->route("admin.program.index")
      ->with("success", "Program unggulan berhasil dihapus!");
  }

  public function toggleStatus(int $id)
  {
    $program = ProgramUnggulan::findOrFail($id);
    $program->is_active = !$program->is_active;
    $program->save();

    $status = $program->is_active ? "diaktifkan" : "dinonaktifkan";
    return redirect()
      ->route("admin.program.index")
      ->with("success", "Program berhasil {$status}!");
  }
}
