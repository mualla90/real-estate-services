<?php

use App\Http\Controllers\Admin\ActivityTypeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminRealtimeController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\BusinessAccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatDemoController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DynamicFieldController;
use App\Http\Controllers\Admin\FcmTokenController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceReviewController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SubcategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/fcm/register-token', [FcmTokenController::class, 'store'])
    ->middleware('auth:admin')
    ->name('admin.fcm-token.store');

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
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        Route::get('/chat-demo', [ChatDemoController::class, 'index'])->name('chat-demo.index');
        Route::post('/chat-demo/conversations', [ChatDemoController::class, 'storeConversation'])->name('chat-demo.conversations.store');
        Route::post('/chat-demo/conversations/{conversation}/messages', [ChatDemoController::class, 'sendMessage'])->name('chat-demo.messages.store');
        Route::post('/chat-demo/pusher/auth', [ChatDemoController::class, 'pusherAuth'])->name('chat-demo.pusher.auth');
        Route::post('/realtime/pusher/auth', [AdminRealtimeController::class, 'pusherAuth'])->name('realtime.pusher.auth');

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->middleware('permission:notifications.view,admin')
            ->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
            ->middleware('permission:notifications.manage,admin')
            ->name('notifications.read-all');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
            ->middleware('permission:notifications.manage,admin')
            ->name('notifications.read');
        Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])
            ->middleware('permission:notifications.view,admin')
            ->name('notifications.open');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
            ->middleware('permission:notifications.manage,admin')
            ->name('notifications.destroy');

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

    // sliders
    Route::get('/sliders', [SliderController::class, 'index'])
        ->middleware('permission:sliders.view,admin')
        ->name('sliders.index');

    Route::get('/sliders/create', [SliderController::class, 'create'])
        ->middleware('permission:sliders.create,admin')
        ->name('sliders.create');

    Route::post('/sliders', [SliderController::class, 'store'])
        ->middleware('permission:sliders.create,admin')
        ->name('sliders.store');

    Route::get('/sliders/{slider}/edit', [SliderController::class, 'edit'])
        ->middleware('permission:sliders.update,admin')
        ->name('sliders.edit');

    Route::put('/sliders/{slider}', [SliderController::class, 'update'])
        ->middleware('permission:sliders.update,admin')
        ->name('sliders.update');

    Route::delete('/sliders/{slider}', [SliderController::class, 'destroy'])
        ->middleware('permission:sliders.delete,admin')
        ->name('sliders.destroy');
        //dynamic fields
    Route::get('/dynamic-fields', [DynamicFieldController::class, 'index'])
        ->middleware('permission:dynamic-fields.view,admin')
        ->name('dynamic-fields.index');

    Route::get('/dynamic-fields/create', [DynamicFieldController::class, 'create'])
        ->middleware('permission:dynamic-fields.create,admin')
        ->name('dynamic-fields.create');

    Route::post('/dynamic-fields', [DynamicFieldController::class, 'store'])
        ->middleware('permission:dynamic-fields.create,admin')
        ->name('dynamic-fields.store');

    Route::get('/dynamic-fields/{dynamicField}/edit', [DynamicFieldController::class, 'edit'])
        ->middleware('permission:dynamic-fields.update,admin')
        ->name('dynamic-fields.edit');

    Route::put('/dynamic-fields/{dynamicField}', [DynamicFieldController::class, 'update'])
        ->middleware('permission:dynamic-fields.update,admin')
        ->name('dynamic-fields.update');

    Route::delete('/dynamic-fields/{dynamicField}', [DynamicFieldController::class, 'destroy'])
        ->middleware('permission:dynamic-fields.delete,admin')
        ->name('dynamic-fields.destroy');
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

    // reports
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:reports.view,admin')
        ->name('reports.index');

    Route::get('/reports/{report}', [ReportController::class, 'show'])
        ->middleware('permission:reports.view,admin')
        ->name('reports.show');

    Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])
        ->middleware('permission:reports.manage,admin')
        ->name('reports.status');


        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

Route::get('/', function () {
    return redirect(url('/admin/login'));
});
