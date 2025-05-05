<?php

use App\Http\Controllers\ScanController;
use App\Http\Controllers\ApiController;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/scan-asset', [ScanController::class, 'scanAsset']);
    Route::get('/scan-asset', [ScanController::class, 'getLatestScansAsset']);

    Route::get('/school', [ApiController::class, 'getsekolah']);
    Route::get('/school/stockOpname/{idSchool}', [ApiController::class, 'getAssetSekolah']);
    Route::post('/school/stockOpname/{idSchool}', [ApiController::class, 'postStockOpname']);
    Route::get('/search/filter', [ApiController::class, 'getSearchFilter']);
    Route::get('/search', [ApiController::class, 'getSearch']);
    Route::get('/item/detail/{id}', [ApiController::class, 'getItemDetail']);
    Route::post('/item/mutation/person/{id}', [ApiController::class, 'mutationPerson']);
    Route::post('/item/mutation/location/{id}', [ApiController::class, 'mutationLocation']);
    Route::post('/item/inspection/{id}', [ApiController::class, 'inspection']);
    Route::put('/item/search/{id}', [ApiController::class, 'updateSearch']);
    Route::get('/user-profile', [ApiController::class, 'profile']);
    Route::post('/logout', [ApiController::class, 'logout']);
});
