<?php

use App\Http\Controllers\HistoryController;

Route::controller(HistoryController::class)->group(function () {
    Route::prefix('history')->group(function () {
        Route::get('/changes', 'changesHistory')->name('histories.changes');
        Route::get('/mutation', 'mutationHistory')->name('histories.mutation');
        Route::get('/location', 'locationHistory')->name('histories.location');
        Route::get('/disposal', 'disposalHistory')->name('histories.disposal');
    });
});
