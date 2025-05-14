<?php

use App\Http\Controllers\ScanController;

Route::controller(ScanController::class)->group(function () {
    Route::resource('/scan', ScanController::class);
    Route::get('/scan/export-found/{scanId}', 'exportFound')->name('scan.exportFound');
    Route::get('/scan/export-missing/{scanId}', 'exportMissing')->name('scan.exportMissing');
    Route::get('/scan/detail/{scan:id}', 'scannedDetail')->name('scanned.detail');
    Route::get('/scan/assets/json', 'scannedAssets')->name('scanned.assets');
    Route::get('/scan/assets/{rfid}/json', 'scannedAssetDetail')->name('scanned.asset.detail');
});
