<?php

use App\Http\Controllers\ApprovalController;

Route::controller(ApprovalController::class)->group(function () {
// Approval
    Route::resource('approval', ApprovalController::class);
    Route::post('/approve', 'approve')->name('approval.approve');
    Route::post('/reject', 'reject')->name('approval.reject');
    Route::get('/mutation', 'mutation')->name('asset.mutation');
    Route::get('/loan', 'loan')->name('asset.loan');
    Route::get('/disposal', 'disposal')->name('asset.disposal');
    Route::get('/mutation/pdf/{id}', 'mutationPdf')->name('mutation.pdf');
    Route::get('/loan/pdf/{id}', 'loanPdf')->name('loan.pdf');
    Route::get('/disposal/pdf/{id}', 'disposalPdf')->name('disposal.pdf');
});
