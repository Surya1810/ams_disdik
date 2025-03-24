<?php

use App\Http\Controllers\KecamatanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// 
Route::get('/kecamatan-data', [KecamatanController::class, 'getKecatamans'])->name('kecamatan.data');
