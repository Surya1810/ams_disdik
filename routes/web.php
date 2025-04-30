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
    Route::get('mutation', [ApprovalController::class, 'mutation'])->name('asset.mutation');
    Route::get('loan', [ApprovalController::class, 'loan'])->name('asset.loan');
    Route::get('disposal', [ApprovalController::class, 'disposal'])->name('asset.disposal');

    /**
     * Date: 28-04-2025
     * Export and Import Asset List
     */
    Route::get('/export/asset', [AssetController::class, 'export'])->name('asset.export');
    Route::post('/import/asset', [AssetController::class, 'import'])->name('asset.import');
    Route::get('/scan/export/found', [ScanController::class, 'exportFound']);
    Route::get('/scan/export/missing', [ScanController::class, 'exportMissing']);
    Route::get('/mutation/pdf/{id}', [ApprovalController::class, 'mutationPdf'])->name('mutation.pdf');
    Route::get('/loan/pdf/{id}', [ApprovalController::class, 'loanPdf'])->name('loan.pdf');
    Route::get('/disposal/pdf/{id}', [ApprovalController::class, 'disposalPdf'])->name('disposal.pdf');


    // Maintenance
    Route::get('/maintenance', [AssetController::class, 'maintenance'])->name('asset.maintenance');


    // History
    Route::get('/histories/changes', [HistoryController::class, 'changesHistory'])->name('histories.changes');
    Route::get('/histories/mutation', [HistoryController::class, 'mutationHistory'])->name('histories.mutation');
    Route::get('/histories/location', [HistoryController::class, 'locationHistory'])->name('histories.location');
    Route::get('/histories/disposal', [HistoryController::class, 'disposalHistory'])->name('histories.disposal');

    // Scan
    Route::resource('scan', ScanController::class);

    // Approval
    Route::resource('approval', ApprovalController::class);
    Route::post('/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/reject', [ApprovalController::class, 'reject'])->name('approval.reject');

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
