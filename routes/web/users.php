<?php

use App\Http\Controllers\UserController;

Route::controller(UserController::class)->group(function () {
    Route::resource('user', UserController::class);
});
