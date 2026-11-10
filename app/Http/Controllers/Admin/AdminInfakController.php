<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminInfakController extends Controller
{
  // INDEX
  public function index(Request $request)
  {
    $query = Infak::with("santri")->orderBy("created_at", "desc");

    if ($request->has("status") && !empty($request->status)) {
      $query->where("status", $request->status);
    }

    if ($request->has("nisa") && !empty($request->nisa)) {
      $query->where("nisa", "LIKE", "%{$request->nisa}%");
    }

    $infaks = $query->paginate(20)->withQueryString();
    $pendingCount = Infak::pending()->count();

    return view("admin.infak.index", compact("infaks", "pendingCount"));
  }

  // KONFIRMASI
  public function konfirmasi(int $id)
  {
    $infak = Infak::findOrFail($id);

    if ($penolakan = $this->bukanPending($infak, "dikonfirmasi")) {
      return $penolakan;
    }

    $infak->status = "lunas";
    $infak->tanggal_bayar = now();
    $infak->user_id = auth()->id();
    $infak->save();

    return redirect()
      ->route("admin.infak.index")
      ->with("success", "✅ Pembayaran {$infak->nama_bulan} {$infak->tahun} (NISA: {$infak->nisa}) berhasil dikonfirmasi!");
  }

  //Pending
  private function bukanPending(Infak $infak, string $aksi)
  {
    if ($infak->status === "pending") {
      return null;
    }

    return redirect()
      ->route("admin.infak.index")
      ->with("warning", "Hanya pembayaran berstatus pending yang bisa {$aksi}.");
  }

  // TOLAK
  public function tolak(int $id)
  {
    $infak = Infak::findOrFail($id);
    if ($penolakan = $this->bukanPending($infak, "ditolak")) {
      return $penolakan;
    }
    $infak->status = "belum_bayar";
    $infak->keterangan = "Ditolak oleh admin: " . auth()->user()->name;
    $infak->save();

    return redirect()
      ->route("admin.infak.index")
      ->with("warning", "❌ Pembayaran {$infak->nama_bulan} {$infak->tahun} (NISA: {$infak->nisa}) ditolak!");
  }

  // 👇 ALL ACCEPT - KONFIRMASI SEMUA PENDING
  public function allAccept()
  {
    $count = Infak::pending()->update([
      "status" => "lunas",
      "tanggal_bayar" => now(),
      "user_id" => auth()->id(),
    ]);

    if ($count == 0) {
      return redirect()
        ->route("admin.infak.index")
        ->with("warning", "Tidak ada pembayaran pending yang perlu dikonfirmasi.");
    }

    return redirect()
      ->route("admin.infak.index")
      ->with("success", "✅ Semua pembayaran pending ({$count} data) berhasil dikonfirmasi!");
  }

  // DESTROY
  public function destroy(int $id)
  {
    $infak = Infak::findOrFail($id);
    $buktiPath = $infak->bukti_pembayaran;

    $infak->delete();

    Infak::hapusBuktiYatim($buktiPath);

    return redirect()
      ->route("admin.infak.index")
      ->with("success", "Data infak berhasil dihapus!");
  }
  // Tampilkan bukti pembayaran: hanya admin yang login (rute ada di grup role:admin)
  public function bukti(int $id)
  {
    $infak = Infak::findOrFail($id);
    $path = $infak->bukti_pembayaran;

    abort_unless($path, 404);

    // Bukti baru ada di penyimpanan privat
    if (Storage::disk("local")->exists($path)) {
      return Storage::disk("local")->response($path, null, [
        "X-Content-Type-Options" => "nosniff",
      ]);
    }

    // Bukti lama (sebelum dipindah) masih di folder publik
    $lama = public_path("images/" . $path);
    abort_unless(file_exists($lama), 404);

    return response()->file($lama, ["X-Content-Type-Options" => "nosniff"]);
  }
}
