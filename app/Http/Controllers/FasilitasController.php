<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;

class FasilitasController extends Controller
{
  public function index()
  {
    $fasilitas = Fasilitas::active()->get();
    return view("fasilitas.index", compact("fasilitas"));
  }

  public function show($slug)
  {
    $fasilitas = Fasilitas::where("slug", $slug)
      ->active()
      ->firstOrFail();
    return view("fasilitas.show", compact("fasilitas"));
  }
}
