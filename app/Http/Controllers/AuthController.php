<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Jika sudah login sebagai admin, redirect ke dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Jika sudah login tapi bukan admin, logout
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Hanya admin yang diizinkan login.');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            // CEK: Apakah user memiliki role admin?
            if (Auth::user()->role !== 'admin') {
                // Jika bukan admin, logout dan beri pesan error
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Hanya admin yang diizinkan login.',
                ])->onlyInput('email');
            }

            // Jika admin, redirect ke dashboard
            return redirect()->route('admin.dashboard')
                             ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }
}