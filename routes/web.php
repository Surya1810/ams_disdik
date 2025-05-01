<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    require __DIR__ . '/web/report.php'; // Report
    require __DIR__ . '/web/assets.php'; // Asset
    require __DIR__ . '/web/history.php'; // History
    require __DIR__ . '/web/approval.php'; // Approval
    require __DIR__ . '/web/scan.php'; // Scan
    require __DIR__ . '/web/tag.php'; // Tag

    /**
     * * Master Data
     */
    require __DIR__ . '/web/users.php'; // User
    require __DIR__ . '/web/sekolah.php'; // Role
    require __DIR__ . '/web/kecamatan.php'; // Permission

    // ! Profile Section - Belum Digunakan
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password/{id}', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
