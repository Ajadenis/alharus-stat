<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminProgramUnggulanController;
use App\Http\Controllers\Admin\AdminKegiatanController;
use App\Http\Controllers\Admin\AdminGuruController;
use App\Http\Controllers\Admin\AdminIsmaController;
use App\Http\Controllers\Admin\AdminFasilitasController;
use App\Http\Controllers\Admin\AdminInfakController;
use App\Http\Controllers\Admin\AdminSantriController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProgramUnggulanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\IsmaController;
use App\Http\Controllers\InfakPublicController;


// ========================================
// ROUTE PUBLIC (Tanpa Login)
// ========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
// Profil (Public)
Route::get('/profil/madrasah', [ProfilController::class, 'madrasah'])->name('profil.madrasah');
Route::get('/profil/guru', [GuruController::class, 'index'])->name('profil.guru');
Route::get('/profil/isma', [IsmaController::class, 'index'])->name('profil.isma');
// Fasilitas (Public)
Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas.index');
Route::get('/fasilitas/{slug}', [FasilitasController::class, 'show'])->name('fasilitas.show');
// Program Unggulan (Public)
Route::get('/program-unggulan', [ProgramUnggulanController::class, 'index'])->name('program-unggulan.index');
Route::get('/program-unggulan/{slug}', [ProgramUnggulanController::class, 'show'])->name('program-unggulan.show');
// Kegiatan (Public)
Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
    Route::get('/', [KegiatanController::class, 'index'])->name('index');
    Route::get('/{slug}', [KegiatanController::class, 'show'])->name('show');
});
// Infak Public
Route::get('/infak', [InfakPublicController::class, 'index'])->name('infak.public');
Route::post('/infak/cari', [InfakPublicController::class, 'cariSantri'])->name('infak.public.cari');
Route::post('/infak/upload', [InfakPublicController::class, 'uploadBukti'])->name('infak.public.upload');
Route::get('/infak/success', [InfakPublicController::class, 'success'])->name('infak.public.success');

// ========================================
// ROUTE AUTH
// ========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ========================================
// ROUTE ADMIN
// ========================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['role:admin'])
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD Kegiatan
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            Route::get('/', [AdminKegiatanController::class, 'index'])->name('index');
            Route::get('/create', [AdminKegiatanController::class, 'create'])->name('create');
            Route::post('/', [AdminKegiatanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminKegiatanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminKegiatanController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminKegiatanController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminKegiatanController::class, 'toggleStatus'])->name('toggle-status');
        });

        // CRUD Guru
        Route::prefix('guru')->name('guru.')->group(function () {
            Route::get('/', [AdminGuruController::class, 'index'])->name('index');
            Route::get('/create', [AdminGuruController::class, 'create'])->name('create');
            Route::post('/', [AdminGuruController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminGuruController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminGuruController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminGuruController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminGuruController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Route ISMA
        Route::prefix('isma')->name('isma.')->group(function () {
            Route::get('/', [AdminIsmaController::class, 'index'])->name('index');
            Route::get('/create', [AdminIsmaController::class, 'create'])->name('create');
            Route::post('/', [AdminIsmaController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminIsmaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminIsmaController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminIsmaController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminIsmaController::class, 'toggleStatus'])->name('toggle-status');
        });

        // CRUD Program Unggulan
        Route::prefix('program')->name('program.')->group(function () {
            Route::get('/', [AdminProgramUnggulanController::class, 'index'])->name('index');
            Route::get('/create', [AdminProgramUnggulanController::class, 'create'])->name('create');
            Route::post('/', [AdminProgramUnggulanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminProgramUnggulanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminProgramUnggulanController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminProgramUnggulanController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminProgramUnggulanController::class, 'toggleStatus'])->name('toggle-status');
        });

        // CRUD Fasilitas
        Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
            Route::get('/', [AdminFasilitasController::class, 'index'])->name('index');
            Route::get('/create', [AdminFasilitasController::class, 'create'])->name('create');
            Route::post('/', [AdminFasilitasController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminFasilitasController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminFasilitasController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminFasilitasController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminFasilitasController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Infak Admin
        Route::prefix('infak')->name('infak.')->group(function () {
            Route::get('/', [AdminInfakController::class, 'index'])->name('index');
            Route::post('/all-accept', [AdminInfakController::class, 'allAccept'])->name('all-accept'); 
            Route::get('/{id}', [AdminInfakController::class, 'show'])->name('show');
            Route::post('/{id}/konfirmasi', [AdminInfakController::class, 'konfirmasi'])->name('konfirmasi');
            Route::get('/{id}/konfirmasi', [AdminInfakController::class, 'konfirmasi'])->name('admin.infak.konfirmasi.get');
            Route::post('/{id}/tolak', [AdminInfakController::class, 'tolak'])->name('tolak');
            Route::delete('/{id}', [AdminInfakController::class, 'destroy'])->name('destroy');
        });

        // CRUD Santri
        Route::prefix('santri')->name('santri.')->group(function () {
            Route::get('/', [AdminSantriController::class, 'index'])->name('index');
            Route::get('/create', [AdminSantriController::class, 'create'])->name('create');
            Route::post('/', [AdminSantriController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminSantriController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminSantriController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminSantriController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminSantriController::class, 'toggleStatus'])->name('toggle-status');
        });
    });