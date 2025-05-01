<?php

use App\Http\Controllers\KecamatanController;

Route::controller(KecamatanController::class)->group(function () {
    Route::resource('kecamatan', KecamatanController::class);
});
