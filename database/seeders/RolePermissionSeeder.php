<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [

            'admins.view',
            'admins.create',
            'admins.update',
            'admins.delete',

            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.assign',

            'business-accounts.view',
            'business-accounts.review',
            'business-accounts.approve',
            'business-accounts.reject',

            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'subcategories.view',
            'subcategories.create',
            'subcategories.update',
            'subcategories.delete',

            'cities.view',
            'cities.create',
            'cities.update',
            'cities.delete',

            'activity-types.view',
            'activity-types.create',
            'activity-types.update',
            'activity-types.delete',

            'sliders.view',
            'sliders.create',
            'sliders.update',
            'sliders.delete',

            'dynamic-fields.view',
            'dynamic-fields.create',
            'dynamic-fields.update',
            'dynamic-fields.delete',

            'reports.view',
            'reports.manage',

            'notifications.view',
            'notifications.manage',

            // 'services.create',
            // 'services.update',
            // 'services.delete',
            'services.view',
            'services.review',
            'services.approve',
            'services.reject',
            'services.activate',
            'services.deactivate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admin',
        ]);

        $businessAccountsManagerRole = Role::firstOrCreate([
            'name' => 'business_accounts_manager',
            'guard_name' => 'admin',
        ]);

        $categoriesManagerRole = Role::firstOrCreate([
            'name' => 'categories_manager',
            'guard_name' => 'admin',
        ]);

        $adminRole->syncPermissions([
            // admins
            'admins.view',
            'admins.create',
            'admins.update',
            'admins.delete',


            // business accounts
            'business-accounts.view',
            'business-accounts.review',
            'business-accounts.approve',
            'business-accounts.reject',

            // categories
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            // subcategories
            'subcategories.view',
            'subcategories.create',
            'subcategories.update',
            'subcategories.delete',

            // cities
            'cities.view',
            'cities.create',
            'cities.update',
            'cities.delete',

            // activity types
            'activity-types.view',
            'activity-types.create',
            'activity-types.update',
            'activity-types.delete',

            // sliders
            'sliders.view',
            'sliders.create',
            'sliders.update',
            'sliders.delete',

            // dynamic fields
            'dynamic-fields.view',
            'dynamic-fields.create',
            'dynamic-fields.update',
            'dynamic-fields.delete',

            // notifications
            'notifications.view',
            'notifications.manage',

            // services
            'services.view',
            'services.review',
            'services.approve',
            'services.reject',
            'services.activate',
            'services.deactivate',

            // notifications
            'notifications.view',
            'notifications.manage',
        ]);

        $businessAccountsManagerRole->syncPermissions([
            'business-accounts.view',
            'business-accounts.review',
            'business-accounts.approve',
            'business-accounts.reject',

            'services.view',
            'services.review',
            'services.approve',
            'services.reject',
            'services.activate',
            'services.deactivate',

            'notifications.view',
            'notifications.manage',
        ]);

        $categoriesManagerRole->syncPermissions([
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'subcategories.view',
            'subcategories.create',
            'subcategories.update',
            'subcategories.delete',

            'cities.view',
            'cities.create',
            'cities.update',
            'cities.delete',

            'activity-types.view',
            'activity-types.create',
            'activity-types.update',
            'activity-types.delete',

            // sliders
            'sliders.view',
            'sliders.create',
            'sliders.update',
            'sliders.delete',

            // dynamic fields
            'dynamic-fields.view',
            'dynamic-fields.create',
            'dynamic-fields.update',
            'dynamic-fields.delete',

            // reports
            'reports.view',
            'reports.manage',

            // notifications
            'notifications.view',
            'notifications.manage',

            // 'services.view',
            // 'services.review',
            // 'services.approve',
            // 'services.reject',
        ]);

        $superAdmin = Admin::where('email', 'superadmin@example.com')->first();

        if ($superAdmin && ! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
