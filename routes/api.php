<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BusinessAccountController;
use App\Http\Controllers\Api\BusinessAccountServiceController;
use App\Http\Controllers\Api\CategoryBrowseController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\DynamicFieldController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\ServiceBrowseController;
use App\Http\Controllers\Api\ServiceRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/services', [ServiceBrowseController::class, 'index']);
Route::get('/services/{service}', [ServiceBrowseController::class, 'show']);
Route::get('/services/{service}/reviews', [ReviewController::class, 'index']);
Route::get('/sliders', [SliderController::class, 'index']);
Route::get('/categories', [CategoryBrowseController::class, 'categories']);
Route::get('/subcategories', [CategoryBrowseController::class, 'subcategories']);


Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);


    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
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

    Route::get('/{businessAccount}/services', [BusinessAccountServiceController::class, 'index']);
    Route::post('/{businessAccount}/services', [BusinessAccountServiceController::class, 'store']);
    Route::get('/{businessAccount}/services/{service}', [BusinessAccountServiceController::class, 'show']);
    Route::put('/{businessAccount}/services/{service}', [BusinessAccountServiceController::class, 'update']);
    Route::delete('/{businessAccount}/services/{service}', [BusinessAccountServiceController::class, 'destroy']);

    Route::get('/{businessAccount}/service-requests/outgoing', [ServiceRequestController::class, 'outgoing']);
    Route::get('/{businessAccount}/service-requests/incoming', [ServiceRequestController::class, 'incoming']);
    Route::post('/{businessAccount}/service-requests', [ServiceRequestController::class, 'store']);
    Route::patch('/{businessAccount}/service-requests/{serviceRequest}/accept', [ServiceRequestController::class, 'accept']);
    Route::patch('/{businessAccount}/service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject']);
    Route::delete('/{businessAccount}/service-requests/{serviceRequest}', [ServiceRequestController::class, 'destroy']);
    Route::post('/{businessAccount}/service-requests/{serviceRequest}/reviews', [ReviewController::class, 'store']);

    Route::get('/{businessAccount}/favorites', [FavoriteController::class, 'index']);
    Route::post('/{businessAccount}/favorites', [FavoriteController::class, 'store']);
    Route::delete('/{businessAccount}/favorites/{service}', [FavoriteController::class, 'destroy']);

    Route::post('/{businessAccount}/reports/services/{service}', [ReportController::class, 'storeForService']);

    Route::get('/{businessAccount}/conversations', [ConversationController::class, 'index']);
    Route::post('/{businessAccount}/conversations', [ConversationController::class, 'store']);
    Route::get('/{businessAccount}/conversations/{conversation}/messages', [ConversationController::class, 'messages']);
    Route::post('/{businessAccount}/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
    Route::patch('/{businessAccount}/conversations/{conversation}/read', [ConversationController::class, 'markRead']);
});

Route::middleware('auth:api')->get('/dynamic-fields', [DynamicFieldController::class, 'index']);
Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'index']);
Route::middleware('auth:api')->patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);
Route::middleware('auth:api')->patch('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
