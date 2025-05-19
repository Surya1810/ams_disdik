<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/school', [ApiController::class, 'getsekolah']);
    Route::get('/districts', [ApiController::class, 'getDistricts']);
    Route::get('/schools/{kecamatanId}', [ApiController::class, 'getSchoolsByDistrict']);
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
