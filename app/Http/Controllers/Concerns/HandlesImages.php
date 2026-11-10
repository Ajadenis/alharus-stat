<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait HandlesImages
{
  /**
   * Simpan gambar ke public/images/{folder}, di-resize maks 2000px.
   * Mengembalikan nama file saja.
   */
  protected function simpanGambar(UploadedFile $file, string $folder, string $nama): string
  {
    $filename = time() . "_" . Str::slug($nama) . "." . $this->ekstensiAman($file);
    $destinationPath = public_path("images/" . $folder);

    if (!file_exists($destinationPath)) {
      mkdir($destinationPath, 0755, true);
    }

    if (!extension_loaded("gd")) {
      Log::warning("Ekstensi GD tidak aktif: gambar disimpan tanpa resize.");
      $file->move($destinationPath, $filename);

      return $filename;
    }
    $manager = ImageManager::usingDriver(Driver::class);
    $image = $manager->decodePath($file->getRealPath());
    $image->scaleDown(width: 2000, height: 2000);
    $image->save($destinationPath . "/" . $filename);

    return $filename;
  }

  /**
   * Hapus file di public/images/{folder}/{filename}.
   * Aman dipanggil jika nama file kosong atau file sudah tidak ada.
   */
  protected function hapusGambar(?string $filename, string $folder = ""): void
  {
    if (!$filename) {
      return;
    }

    $fullPath = public_path("images/" . ($folder ? $folder . "/" : "") . $filename);

    if (file_exists($fullPath)) {
      unlink($fullPath);
    }
  }
  // Ekstensi ditentukan dari ISI file (bukan nama kiriman pengguna) dan dibatasi daftar aman
  protected function ekstensiAman(UploadedFile $file): string
  {
    $ext = strtolower((string) $file->guessExtension());

    abort_unless(in_array($ext, ["jpg", "jpeg", "png", "webp", "gif", "bmp"], true), 422, "Format gambar tidak didukung.");

    return $ext;
  }
}
