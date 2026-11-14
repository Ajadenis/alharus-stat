<?php

namespace App\Http\Controllers;

use App\Models\Guru;

class GuruController extends Controller
{
  public function index()
  {
    $guru = Guru::active()->get();
    return view("profil.guru", compact("guru"));
  }
}
