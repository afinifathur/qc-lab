<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\ReportPdfController;

/*
|--------------------------------------------------------------------------
| Public redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => auth()->check()
    ? redirect()->route('samples.index')
    : redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Auth (guest / auth)
|--------------------------------------------------------------------------
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected (must login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ===== Samples =====
    Route::get('/samples',               [SampleController::class, 'index'])->name('samples.index');
    Route::get('/samples/new',           [SampleController::class, 'create'])->name('samples.create');
    Route::post('/samples',              [SampleController::class, 'store'])->name('samples.store');
	
	// Submit draft → SUBMITTED
	Route::post('/samples/{sample}/submit', [\App\Http\Controllers\WorkflowController::class, 'submit'])
    ->name('samples.submit');


    Route::get('/samples/{sample}/edit', [SampleController::class, 'edit'])->name('samples.edit');
    Route::put('/samples/{sample}',      [SampleController::class, 'update'])->name('samples.update');

    // Soft delete (ke Recycle Bin)
    Route::delete('/samples/{sample}',   [SampleController::class,'destroy'])->name('samples.destroy');

    // ===== Approvals =====
    Route::get('/approvals',             [WorkflowController::class, 'queue'])->name('approvals.index');
    Route::post('/approvals/{sample}',   [WorkflowController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{sample}/reject', [WorkflowController::class,'reject'])->name('approvals.reject');

    // ===== Reports (preview / download) =====
    Route::get('/reports/{sample}/pdf',  [ReportPdfController::class, 'show'])->name('reports.pdf');

    // ===== Recycle Bin =====
    Route::get('/recycle-bin',                 [SampleController::class,'recycle'])->name('samples.recycle');
    Route::post('/recycle-bin/{id}/restore',   [SampleController::class,'restore'])->name('samples.restore');
    Route::delete('/recycle-bin/{id}/force',   [SampleController::class,'forceDelete'])->name('samples.force');
});
