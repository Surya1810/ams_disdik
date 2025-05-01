<?php

use App\Http\Controllers\ReportController;

Route::controller(ReportController::class)->group(function () {
    Route::prefix('dashboard')->group(function () {
       Route::get('', 'index')->name('dashboard');
       Route::prefix('/json')->group(function () {
           Route::get('/aset-per-tahun', 'getNilaiPerTahunJSON')->name('report.json.aset-per-tahun');
           Route::get('/aset-per-sekolah', 'getNilaiPerSekolahJSON')->name('report.json.aset-per-sekolah');
           Route::get('/aset-per-kecamatan', 'getNilaiPerKecamatanJSON')->name('report.json.aset-per-kecamatan');
       });
    });
});
