<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  /**
   * Tampilkan halaman login
   */
  public function showLogin()
  {
    // Jika sudah login sebagai admin, redirect ke dashboard
    if (Auth::check() && Auth::user()->role === "admin") {
      return redirect()->route("admin.dashboard");
    }

    // Jika sudah login tapi bukan admin, logout
    if (Auth::check()) {
      Auth::logout();
      return redirect()
        ->route("login")
        ->with("error", "Hanya admin yang diizinkan login.");
    }

    return view("auth.login");
  }

  /**
   * Proses login
   */
  public function login(Request $request)
  {
    $request->validate([
      "email" => "required|email",
      "password" => "required",
    ]);

    // role ikut jadi syarat: akun non-admin gagal login seperti kata sandi salah
    $credentials = $request->only("email", "password");
    $credentials["role"] = "admin";

    if (Auth::attempt($credentials, $request->has("remember"))) {
      $request->session()->regenerate();

      return redirect()
        ->route("admin.dashboard")
        ->with("success", "Selamat datang, " . Auth::user()->name . "!");
    }

    return back()
      ->withErrors([
        "email" => "Email atau password salah.",
      ])
      ->onlyInput("email");
  }

  /**
   * Proses logout
   */
  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect("/login")->with("success", "Anda berhasil logout.");
  }
}
