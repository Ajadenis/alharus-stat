<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function madrasah()
    {
        return view('profil.madrasah');
    }

    public function guru()
    {
        return view('profil.guru');
    }

    public function isma()
    {
        return view('profil.isma');
    }
}
