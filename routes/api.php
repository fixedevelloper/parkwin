<?php

use App\Http\Controllers\SavingController;
use App\Http\Controllers\ScheduledSavingController;
use App\Models\Tour;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TontineController;
use App\Http\Controllers\PaymentController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

/*Route::middleware('auth:sanctum')->group(function(){
    Route::get('/tontines',[TontineController::class,'index']);
    Route::post('/tontines',[TontineController::class,'store']);
    Route::post('/tontines/{id}/join',[TontineController::class,'join']);

    Route::post('/sessions/{id}/pay',[PaymentController::class,'pay']);
});*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class,'profile']);
    Route::post('/profile', [AuthController::class,'updateProfile']);
    Route::get('/tontines', [TontineController::class,'index']);
    Route::post('/tontines', [TontineController::class,'store']);
    Route::get('/tontines/{id}', [TontineController::class,'show']);
    Route::get('/tontines/{id}/sessions',[TontineController::class,'listTours']
    );
    Route::get('/tontines/{reference}/find',[TontineController::class,'findByReference']
    );
    Route::get('/tontines/{id}/join',[TontineController::class,'join']
    );
    Route::post('/tontines/pay', [PaymentController::class,'pay']);
    /** EPARGNE */
    Route::get('/savings', [SavingController::class, 'index']);
    Route::get('/savings/{id}', [SavingController::class, 'show']);
    Route::post('/savings', [SavingController::class, 'store']);
    Route::post('/savings/{id}/deposit', [SavingController::class, 'deposit']);
    Route::get('/savings/payments/{id}', [SavingController::class, 'payments']);
    Route::post('/savings/deposit', [SavingController::class,'deposit']);
    Route::post('/savings/withdraw', [SavingController::class,'withdraw']);
    Route::post('/scheduled-savings', [ScheduledSavingController::class,'store']);
    Route::patch('/scheduled-savings/{id}/toggle', [ScheduledSavingController::class,'toggle']);

    Route::get('/payments/history', [PaymentController::class,'history']);
    Route::get('/payments/tontine/{id}', [PaymentController::class,'historyByTontine']);
});

