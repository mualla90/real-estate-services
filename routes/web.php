<?php

use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\BusinessAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/business-accounts', [BusinessAccountController::class, 'index'])
            ->middleware('permission:business-accounts.view,admin')
            ->name('business-accounts.index');

        Route::get('/business-accounts/{businessAccount}', [BusinessAccountController::class, 'show'])
            ->middleware('permission:business-accounts.view,admin')
            ->name('business-accounts.show');

        Route::patch('/business-accounts/{businessAccount}/approve', [BusinessAccountController::class, 'approve'])
            ->middleware('permission:business-accounts.approve,admin')
            ->name('business-accounts.approve');

        Route::patch('/business-accounts/{businessAccount}/reject', [BusinessAccountController::class, 'reject'])
            ->middleware('permission:business-accounts.reject,admin')
            ->name('business-accounts.reject');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

Route::redirect('/', '/admin/login');
