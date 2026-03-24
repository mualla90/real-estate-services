<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BusinessAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);


    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    Route::middleware('auth:api')->get('/me', function (Request $request) {
    return $request->user();
});
});

Route::middleware('auth:api')->prefix('business-accounts')->group(function () {
    Route::get('/', [BusinessAccountController::class, 'index']);
    Route::post('/', [BusinessAccountController::class, 'store']);
    Route::get('/{businessAccount}', [BusinessAccountController::class, 'show']);
    Route::put('/{businessAccount}', [BusinessAccountController::class, 'update']);
});



