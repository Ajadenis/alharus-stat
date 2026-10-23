<?php

namespace App\Http\Controllers;

use App\Models\ProgramUnggulan;
use Illuminate\Http\Request;

class ProgramUnggulanController extends Controller
{
    public function index()
    {
        $programs = ProgramUnggulan::where('is_active', true)->get();
        return view('program-unggulan.index', compact('programs'));
    }

    public function show($slug)
    {
        $program = ProgramUnggulan::where('slug', $slug)
                                   ->where('is_active', true)
                                   ->firstOrFail();
        return view('program-unggulan.show', compact('program'));
    }
}