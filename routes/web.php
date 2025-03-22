<?php

use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('dashboard');
    // return redirect()->route('dashboard');
});

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Auth::routes();
Route::middleware('auth')->group(function () {
    // Dashboard

    // =========================================================== Asset ==================================================================


    // RFID Menu
    Route::resource('tag', TagController::class);
    Route::get('/export/tag', [TagController::class, 'export'])->name('tag.export');

    // =========================================================== Master Data ==================================================================
    // User
    Route::resource('user', UserController::class);

    // Sekolah

    // Kecamatan

    // Lokasi
});
