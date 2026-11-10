<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\Santri;
use Illuminate\Http\Request;
use App\Helpers\SantriHelper;
use Illuminate\Support\Str;
use App\Http\Controllers\Concerns\HandlesImages;
use App\Models\Infak;

class AdminSantriController extends Controller
{
  use HandlesImages;
  public function index(Request $request)
  {
    $query = Santri::query();

    if ($request->has("search") && !empty($request->search)) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where("nama", "LIKE", "%{$search}%")
          ->orWhere("nisa", "LIKE", "%{$search}%")
          ->orWhere("nis", "LIKE", "%{$search}%");
      });
    }

    if ($request->has("kelas") && !empty($request->kelas)) {
      $query->where("kelas_sekarang", $request->kelas);
    }

    if ($request->has("jenis_kelamin") && !empty($request->jenis_kelamin)) {
      $query->where("jenis_kelamin", $request->jenis_kelamin);
    }

    if ($request->has("status") && !empty($request->status) && $request->status != "semua") {
      $query->where("status_santri", $request->status);
    }

    $santri = $query
      ->latest()
      ->paginate(10)
      ->withQueryString();
    $kelasList = Santri::KELAS;

    return view("admin.santri.index", compact("santri", "kelasList"));
  }

  private function rules(?int $id = null): array
  {
    return [
      "nama" => "required|string|max:255",
      "nis" => ["required", "string", Rule::unique("santris", "nis")->ignore($id)],
      "nisa" => ["required", "string", Rule::unique("santris", "nisa")->ignore($id)],
      "tahun_masuk" => "required|integer|min:2000|max:" . date("Y"),
      "kelas_sekarang" => ["nullable", Rule::in(Santri::KELAS)],
      "status_santri" => "required|in:aktif,lulus,keluar",
      "jenis_kelamin" => "required|in:Laki-laki,Perempuan",
      "alamat" => "nullable|string",
      "tanggal_lahir" => "nullable|date",
      "nama_orang_tua" => "nullable|string|max:255",
      "nama_wali" => "nullable|string|max:255",
      "telepon_wali" => "nullable|string|max:20",
      "foto" => "nullable|image|mimes:jpeg,png,jpg,webp|max:2048",
    ];
  }

  // Validasi, lalu siapkan data: kelas otomatis, status, is_active, dan foto
  private function dataTervalidasi(Request $request, ?Santri $santri = null): array
  {
    $data = $request->validate($this->rules($santri?->id));

    // Kelas otomatis dari tahun masuk kalau tidak dipilih
    if (empty($data["kelas_sekarang"])) {
      $data["kelas_sekarang"] = SantriHelper::hitungKelas($data["tahun_masuk"]);
    }

    // Sudah lulus menurut kelas: statusnya ikut lulus
    if ($data["kelas_sekarang"] === "Lulus" && $data["status_santri"] === "aktif") {
      $data["status_santri"] = "lulus";
    }

    // is_active selalu mengikuti status_santri
    $data["is_active"] = $data["status_santri"] === "aktif";

    // Foto (tanpa resize, batas 2 MB)
    if ($request->hasFile("foto")) {
      $this->hapusGambar($santri?->foto, "santri");

      $file = $request->file("foto");
      $filename = time() . "_" . Str::slug($data["nama"]) . "." . $this->ekstensiAman($file);

      $folder = public_path("images/santri");
      if (!file_exists($folder)) {
        mkdir($folder, 0755, true);
      }

      $file->move($folder, $filename);
      $data["foto"] = $filename;
    }

    return $data;
  }

  public function create()
  {
    return view("admin.santri.create");
  }

  public function store(Request $request)
  {
    Santri::create($this->dataTervalidasi($request));

    return redirect()
      ->route("admin.santri.index")
      ->with("success", "Santri berhasil ditambahkan!");
  }

  public function edit(int $id)
  {
    $santri = Santri::findOrFail($id);
    return view("admin.santri.edit", compact("santri"));
  }

  public function update(Request $request, $id)
  {
    $santri = Santri::findOrFail($id);
    $santri->update($this->dataTervalidasi($request, $santri));

    return redirect()
      ->route("admin.santri.index")
      ->with("success", "Santri berhasil diperbarui!");
  }

  public function destroy(int $id)
  {
    $santri = Santri::findOrFail($id);

    $this->hapusGambar($santri->foto, "santri");

    $buktiList = Infak::where("santri_id", $santri->id)
      ->pluck("bukti_pembayaran")
      ->filter()
      ->unique();

    $santri->delete();

    foreach ($buktiList as $path) {
      Infak::hapusBuktiYatim($path);
    }

    return redirect()
      ->route("admin.santri.index")
      ->with("success", "Santri berhasil dihapus!");
  }

  public function toggleStatus(int $id)
  {
    $santri = Santri::findOrFail($id);

    // Alumni tidak diubah lewat toggle; ubah statusnya lewat form edit
    if ($santri->status_santri === "lulus") {
      return redirect()
        ->route("admin.santri.index")
        ->with("warning", "Santri sudah lulus. Ubah statusnya lewat form edit jika perlu.");
    }

    $santri->is_active = !$santri->is_active;
    $santri->status_santri = $santri->is_active ? "aktif" : "keluar";
    $santri->save();

    $status = $santri->is_active ? "diaktifkan" : "dinonaktifkan";
    return redirect()
      ->route("admin.santri.index")
      ->with("success", "Santri berhasil {$status}!");
  }
}
