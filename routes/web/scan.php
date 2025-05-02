<?php

use App\Http\Controllers\ScanController;

Route::controller(ScanController::class)->group(function () {
    Route::resource('/scan', ScanController::class);
    Route::get('/scan/export/found', 'exportFound');
    Route::get('/scan/export/missing', 'exportMissing');
    Route::get('/api/scanned/assets', 'scannedAssets')->name('scanned.assets');
});
