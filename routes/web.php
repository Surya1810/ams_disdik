<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

Route::get('/', function () {
    return redirect()->route('dashboard');
    // return view('welcome');
});

Auth::routes();
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Report
    Route::resource('report', ReportController::class);


    // Profile Section
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password/{id}', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================== Asset ==================================================================
    // Asset
    Route::resource('asset', AssetController::class);

    // RFID Menu
    Route::resource('tag', TagController::class);
    Route::get('/export/tag', [TagController::class, 'export'])->name('tag.export');

    // =========================================================== Master Data ==================================================================
    // User
    Route::resource('user', UserController::class);

    // Sekolah
    Route::resource('sekolah', SekolahController::class);

    // Kecamatan
    Route::resource('kecamatan', KecamatanController::class);

    // Lokasi
});
