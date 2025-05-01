<?php

use App\Http\Controllers\SekolahController;

Route::controller(SekolahController::class)->group(function () {
    Route::resource('sekolah', SekolahController::class);
});
