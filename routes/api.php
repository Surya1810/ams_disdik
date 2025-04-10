<?php

use App\Http\Controllers\ScanController;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/assets', function () {
    return response()->json(
        Asset::with(['tag:rfid_number,rfid_number'])->get()
    );
});

Route::post('/scan-asset', [ScanController::class, 'scanAsset']);
Route::get('/scan-asset', [ScanController::class, 'getLatestScansAsset']);
