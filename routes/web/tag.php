<?php

use App\Http\Controllers\TagController;

Route::controller(TagController::class)->group(function () {
    Route::resource('tag', TagController::class);
    Route::get('/export/tag', [TagController::class, 'export'])->name('tag.export');
    Route::post('/tag/distribute', [TagController::class, 'distribute'])->name('tag.distribute');
});
