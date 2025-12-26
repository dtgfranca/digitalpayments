<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/customers', [CustomerController::class, 'store']);

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/transfers', [TransferController::class, 'transfer']);
});
