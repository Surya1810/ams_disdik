<?php

use App\Http\Controllers\TagController;

Route::controller(TagController::class)->group(function () {
    Route::resource('tag', TagController::class)
        ->except(['store']);
    Route::post('/tag', 'store')->middleware('auth.only.admin')->name('tag.store');
    Route::get('/export/tag', [TagController::class, 'export'])->name('tag.export');
    Route::post('/tag/distribute', [TagController::class, 'distribute'])
        ->middleware('auth.only.operator')
        ->name('tag.distribute');
});
