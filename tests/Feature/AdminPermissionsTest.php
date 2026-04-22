<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login_on_protected_route(): void
    {
        $this->get('/admin/reports')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_without_permissions_gets_403_on_protected_routes(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin');

        $this->getJson('/admin/reports')->assertForbidden();
        $this->getJson('/admin/notifications')->assertForbidden();
        $this->getJson('/admin/services/review')->assertForbidden();
    }

    public function test_admin_with_reports_view_permission_can_access_reports_index(): void
    {
        $admin = $this->makeAdminWithPermissions(['reports.view']);
        $this->assertTrue($admin->can('reports.view'));

        $route = app('router')->getRoutes()->getByName('admin.reports.index');
        $this->assertNotNull($route);
        $this->assertContains('permission:reports.view,admin', $route->gatherMiddleware());
    }

    public function test_admin_with_notifications_manage_permission_can_mark_all_read(): void
    {
        $admin = $this->makeAdminWithPermissions(['notifications.manage']);
        $this->actingAs($admin, 'admin');

        $this->post('/admin/notifications/read-all')->assertStatus(302);
    }

    public function test_admin_with_services_view_permission_can_access_service_review_index(): void
    {
        $admin = $this->makeAdminWithPermissions(['services.view']);
        $this->assertTrue($admin->can('services.view'));

        $route = app('router')->getRoutes()->getByName('admin.services.review.index');
        $this->assertNotNull($route);
        $this->assertContains('permission:services.view,admin', $route->gatherMiddleware());
    }

    protected function makeAdmin(): Admin
    {
        return Admin::query()->create([
            'name' => 'Test Admin',
            'email' => 'admin_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    protected function makeAdminWithPermissions(array $permissions): Admin
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = $this->makeAdmin();

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'admin');
        }

        $admin->givePermissionTo($permissions);

        return $admin;
    }
}
