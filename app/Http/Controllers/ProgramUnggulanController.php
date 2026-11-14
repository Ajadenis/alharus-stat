<?php

namespace App\Http\Controllers;

use App\Models\ProgramUnggulan;

class ProgramUnggulanController extends Controller
{
  public function index()
  {
    $programs = ProgramUnggulan::active()->get();
    return view("program-unggulan.index", compact("programs"));
  }

  public function show($slug)
  {
    $program = ProgramUnggulan::where("slug", $slug)
      ->active()
      ->firstOrFail();
    return view("program-unggulan.show", compact("program"));
  }
}
