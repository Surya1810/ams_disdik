<?php

use App\Http\Controllers\AssetController;

Route::controller(AssetController::class)->group(function () {
    Route::resource('asset', AssetController::class);
    Route::get('/asset/templates/download', 'downloadTemplateImport')
        ->name('asset.download.template.import');
    Route::get('/asset/{id}/download', [AssetController::class, 'download'])->name('asset.download');
    Route::post('/asset/mark-maintained', 'markAsMaintained')->name('asset.markMaintained');
    Route::get('/export/asset', 'export')->name('asset.export');
    Route::post('/import/asset', 'import')->name('asset.import');
    Route::get('/maintenance', 'maintenance')->name('asset.maintenance');
    Route::get('/maintenance/pdf', 'maintenancePdf')->name('maintenance.pdf');
});
