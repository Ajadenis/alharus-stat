<?php

namespace App\Http\Controllers;

use App\Models\Isma;

class IsmaController extends Controller
{
  public function index()
  {
    $isma = Isma::active()->get();
    return view("profil.isma", compact("isma"));
  }
}
