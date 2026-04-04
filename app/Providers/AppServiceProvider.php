<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    //      Gate::before(function ($user, string $ability) {
    //     return method_exists($user, 'hasRole') && $user->hasRole('super_admin') ? true : null;
    // });

        Gate::before(function ($user, string $ability) {
            if ($user instanceof Admin && $user->hasRole('super_admin')) {
                return true;
            }

            return null;
        });
    }
}
