<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;

class HomeController extends Controller
{
  public function index()
  {
    $kegiatanTerbaru = Kegiatan::where("is_active", true)
      ->latest()
      ->limit(3)
      ->get();

    return view("home.index", compact("kegiatanTerbaru"));
  }
}
