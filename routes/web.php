<?php

use App\Http\Controllers\Admin\ActivityTypeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\BusinessAccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceReviewController;
use App\Http\Controllers\Admin\SubcategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
    Route::get('/lang/{locale}', function ($locale) {
        if (! in_array($locale, ['en', 'ar'], true)) {
            abort(400);
        }

        session(['locale' => $locale]);

        return back();
    })->name('lang.switch');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

            //admins
        Route::get('/admins', [AdminController::class, 'index'])
            ->middleware('permission:admins.view,admin')
            ->name('admins.index');

        Route::get('/admins/create', [AdminController::class, 'create'])
            ->middleware('permission:admins.create,admin')
            ->name('admins.create');

        Route::post('/admins', [AdminController::class, 'store'])
            ->middleware('permission:admins.create,admin')
            ->name('admins.store');

        Route::get('/admins/{admin}/edit', [AdminController::class, 'edit'])
            ->middleware('permission:admins.update,admin')
            ->name('admins.edit');

        Route::put('/admins/{admin}', [AdminController::class, 'update'])
            ->middleware('permission:admins.update,admin')
            ->name('admins.update');

            Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])
            ->middleware('permission:admins.delete,admin')
            ->name('admins.destroy');
                //roles
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view,admin')
            ->name('roles.index');

        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create,admin')
            ->name('roles.create');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create,admin')
            ->name('roles.store');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.update,admin')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.update,admin')
            ->name('roles.update');

        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete,admin')
            ->name('roles.destroy');
                    //buisness_accounts
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

                //cities
        Route::get('/cities', [CityController::class, 'index'])
            ->middleware('permission:cities.view,admin')
            ->name('cities.index');

        Route::get('/cities/create', [CityController::class, 'create'])
            ->middleware('permission:cities.create,admin')
            ->name('cities.create');

        Route::post('/cities', [CityController::class, 'store'])
            ->middleware('permission:cities.create,admin')
            ->name('cities.store');

        Route::get('/cities/{city}/edit', [CityController::class, 'edit'])
            ->middleware('permission:cities.update,admin')
            ->name('cities.edit');

        Route::put('/cities/{city}', [CityController::class, 'update'])
            ->middleware('permission:cities.update,admin')
            ->name('cities.update');

        Route::delete('/cities/{city}', [CityController::class, 'destroy'])
            ->middleware('permission:cities.delete,admin')
            ->name('cities.destroy');
            //activity_type
        Route::get('/activity-types', [ActivityTypeController::class, 'index'])
            ->middleware('permission:activity-types.view,admin')
            ->name('activity-types.index');

        Route::get('/activity-types/create', [ActivityTypeController::class, 'create'])
            ->middleware('permission:activity-types.create,admin')
            ->name('activity-types.create');

        Route::post('/activity-types', [ActivityTypeController::class, 'store'])
            ->middleware('permission:activity-types.create,admin')
            ->name('activity-types.store');

        Route::get('/activity-types/{activityType}/edit', [ActivityTypeController::class, 'edit'])
            ->middleware('permission:activity-types.update,admin')
            ->name('activity-types.edit');

        Route::put('/activity-types/{activityType}', [ActivityTypeController::class, 'update'])
            ->middleware('permission:activity-types.update,admin')
            ->name('activity-types.update');

        Route::delete('/activity-types/{activityType}', [ActivityTypeController::class, 'destroy'])
            ->middleware('permission:activity-types.delete,admin')
            ->name('activity-types.destroy');
            //category
    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('permission:categories.view,admin')
        ->name('categories.index');

    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->middleware('permission:categories.create,admin')
        ->name('categories.create');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('permission:categories.create,admin')
        ->name('categories.store');

    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:categories.update,admin')
        ->name('categories.edit');

    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:categories.update,admin')
        ->name('categories.update');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete,admin')
        ->name('categories.destroy');

        //subcategory
    Route::get('/subcategories', [SubcategoryController::class, 'index'])
        ->middleware('permission:subcategories.view,admin')
        ->name('subcategories.index');

    Route::get('/subcategories/create', [SubcategoryController::class, 'create'])
        ->middleware('permission:subcategories.create,admin')
        ->name('subcategories.create');

    Route::post('/subcategories', [SubcategoryController::class, 'store'])
        ->middleware('permission:subcategories.create,admin')
        ->name('subcategories.store');

    Route::get('/subcategories/{subcategory}/edit', [SubcategoryController::class, 'edit'])
        ->middleware('permission:subcategories.update,admin')
        ->name('subcategories.edit');

    Route::put('/subcategories/{subcategory}', [SubcategoryController::class, 'update'])
        ->middleware('permission:subcategories.update,admin')
        ->name('subcategories.update');

    Route::delete('/subcategories/{subcategory}', [SubcategoryController::class, 'destroy'])
        ->middleware('permission:subcategories.delete,admin')
        ->name('subcategories.destroy');
        //services
    Route::get('/services/review', [ServiceReviewController::class, 'index'])
    ->middleware('permission:services.view,admin')
    ->name('services.review.index');

    Route::get('/services/{service}/review', [ServiceReviewController::class, 'show'])
        ->middleware('permission:services.view,admin')
        ->name('services.review.show');

    Route::post('/services/{service}/approve', [ServiceReviewController::class, 'approve'])
        ->middleware('permission:services.approve,admin')
        ->name('services.approve');

    Route::post('/services/{service}/reject', [ServiceReviewController::class, 'reject'])
        ->middleware('permission:services.reject,admin')
        ->name('services.reject');

    Route::patch('/services/{service}/activate', [ServiceReviewController::class, 'activate'])
        ->middleware('permission:services.activate,admin')
        ->name('services.activate');

    Route::patch('/services/{service}/deactivate', [ServiceReviewController::class, 'deactivate'])
        ->middleware('permission:services.deactivate,admin')
        ->name('services.deactivate');


        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

Route::redirect('/', '/admin/login');
