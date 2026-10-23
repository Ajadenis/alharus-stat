<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::where('is_active', true)->get();
        return view('profil.guru', compact('guru'));
    }
}