<?php

use App\Http\Controllers\ProfileController;

Route::controller(ProfileController::class)
    ->group(function () {
        Route::prefix('profile')
            ->group(function () {
                Route::get('', 'edit')->name('profile.edit');
                Route::put('/update/{id}', 'update')->name('profile.update');
                Route::put('/password/{id}', 'password')->name('profile.password');
            });
    });
