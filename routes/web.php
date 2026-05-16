<?php

use App\Http\Controllers\PeriodeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\AspekController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BuktiDukungController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordController;


Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Aspek & Indikator — taruh PALING ATAS sebelum route lain
    Route::get('aspek/export', [AspekController::class, 'export'])->name('aspek.export');
    Route::post('aspek/import', [AspekController::class, 'import'])->name('aspek.import');
    Route::get('aspek', [AspekController::class, 'index'])->name('aspek.index');
    Route::post('aspek', [AspekController::class, 'storeAspek'])->name('aspek.store');
    Route::patch('aspek/{aspect}', [AspekController::class, 'updateAspek'])->name('aspek.update');
    Route::post('aspek/{aspect}/indikator', [AspekController::class, 'storeIndikator'])->name('aspek.indikator.store');
    Route::delete('aspek/{aspect}', [AspekController::class, 'destroyAspek'])->name('aspek.destroy');
    Route::patch('indikator/{indicator}', [AspekController::class, 'updateIndikator'])->name('indikator.update');
    Route::delete('indikator/{indicator}', [AspekController::class, 'destroyIndikator'])->name('indikator.destroy');

    // Periode Penilaian
    Route::get('periode', [PeriodeController::class, 'index'])->name('periode.index');
    Route::post('periode', [PeriodeController::class, 'store'])->name('periode.store');
    Route::patch('periode/{period}/activate', [PeriodeController::class, 'activate'])->name('periode.activate');
    Route::delete('periode/{period}', [PeriodeController::class, 'destroy'])->name('periode.destroy');

    // Manajemen User
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Bukti Dukung
    Route::get('bukti-dukung', [BuktiDukungController::class, 'index'])->name('bukti.index');
    Route::patch('bukti-dukung/{indicator}/toggle', [BuktiDukungController::class, 'toggle'])->name('bukti.toggle');
    
    // Validasi
    Route::get('validasi', [ValidasiController::class, 'index'])->name('validasi.index');
    Route::post('validasi/{submission}/review', [ValidasiController::class, 'review'])->name('validasi.review');

    // Ganti Password
    Route::get('password/edit', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('password/edit', [PasswordController::class, 'update'])->name('password.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Semua role bisa akses
    Route::get('bukti-dukung', [BuktiDukungController::class, 'index'])->name('bukti.index');
    Route::patch('bukti-dukung/{indicator}/toggle', [BuktiDukungController::class, 'toggle'])->name('bukti.toggle');

    // Validator & Admin
    Route::middleware('role:validator,admin')->group(function () {
        Route::get('validasi', [ValidasiController::class, 'index'])->name('validasi.index');
        Route::post('validasi/{submission}/review', [ValidasiController::class, 'review'])->name('validasi.review');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::post('periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::patch('periode/{period}/activate', [PeriodeController::class, 'activate'])->name('periode.activate');
        Route::delete('periode/{period}', [PeriodeController::class, 'destroy'])->name('periode.destroy');

        Route::get('aspek', [AspekController::class, 'index'])->name('aspek.index');
        Route::post('aspek', [AspekController::class, 'storeAspek'])->name('aspek.store');
        Route::post('aspek/{aspect}/indikator', [AspekController::class, 'storeIndikator'])->name('aspek.indikator.store');
        Route::delete('aspek/{aspect}', [AspekController::class, 'destroyAspek'])->name('aspek.destroy');
        Route::delete('indikator/{indicator}', [AspekController::class, 'destroyIndikator'])->name('indikator.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::patch('aspek/{aspect}', [AspekController::class, 'updateAspek'])->name('aspek.update');
        Route::patch('indikator/{indicator}', [AspekController::class, 'updateIndikator'])->name('indikator.update');

        Route::post('aspek/import', [AspekController::class, 'import'])->name('aspek.import');
        Route::get('aspek/export', [AspekController::class, 'export'])->name('aspek.export');
    });

    // Ganti password & profil — semua role
    Route::get('password/edit', [PasswordController::class, 'edit'])->name('mypassword.edit');
    Route::put('password/edit', [PasswordController::class, 'update'])->name('mypassword.update');
});

require __DIR__.'/settings.php';