<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('dashboard');
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

    // Maintenance
    Route::get('maintenance', function () {
        return view('asset.maintenance');
    })->name('maintenance.index');
    // Mutation
    Route::get('mutation', function () {
        return view('asset.mutation');
    })->name('mutation.index');
    // Disposal
    Route::get('disposal', function () {
        return view('asset.disposal');
    })->name('disposal.index');

    // History
    Route::get('/histories/changes', [HistoryController::class, 'changesHistory'])->name('histories.changes');
    Route::get('/histories/mutation', [HistoryController::class, 'mutationHistory'])->name('histories.mutation');
    Route::get('/histories/location', [HistoryController::class, 'locationHistory'])->name('histories.location');

    // Scan
    Route::resource('scan', ScanController::class);

    // Approval
    Route::resource('approval', ApprovalController::class);
    Route::post('/approval/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    Route::post('/approval/bulk-approve', [ApprovalController::class, 'bulkApprove'])->name('approval.bulk.approve');
    Route::post('/approval/bulk-reject', [ApprovalController::class, 'bulkReject'])->name('approval.bulk.reject');


    // RFID Menu
    Route::resource('tag', TagController::class);
    Route::get('/export/tag', [TagController::class, 'export'])->name('tag.export');
    Route::post('/tag/distribute', [TagController::class, 'distribute'])->name('tag.distribute');


    // =========================================================== Master Data ==================================================================
    // User
    Route::resource('user', UserController::class);

    // Sekolah
    Route::resource('sekolah', SekolahController::class);

    // Kecamatan
    Route::resource('kecamatan', KecamatanController::class);
});
