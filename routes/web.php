<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

// Get Images to GCS use static token
Route::get('/asset/images/{path}', [AssetController::class, 'streamImage'])
        ->name('asset.image.stream');

Route::middleware('auth')->group(function () {

    require __DIR__ . '/web/report.php'; // Report

    Route::middleware('auth.not.admin')->group(function() {
        require __DIR__ . '/web/assets.php'; // Asset
        require __DIR__ . '/web/history.php'; // History
        require __DIR__ . '/web/approval.php'; // Approval
        require __DIR__ . '/web/scan.php'; // Scan
    });
    Route::get('/loan/json/schools/{kecamatan:id}', [ApprovalController::class, 'getSchoolsByDistrictJSON'])->name('loan.json.schools');

    require __DIR__ . '/web/tag.php'; // Tag
    require __DIR__ . '/web/profile.php'; // Profile

    /**
     * * Master Data
     */
    require __DIR__ . '/web/users.php'; // User
    require __DIR__ . '/web/sekolah.php'; // Role
    require __DIR__ . '/web/kecamatan.php'; // Permission
});
