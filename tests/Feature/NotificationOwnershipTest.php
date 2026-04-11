<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_user_cannot_mark_read_notification_that_belongs_to_another_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $notificationForB = $userB->appNotifications()->create([
            'type' => 'custom',
            'title' => 'Title',
            'message' => 'Message',
        ]);

        Passport::actingAs($userA);

        $this->patchJson("/api/notifications/{$notificationForB->id}/read")
            ->assertStatus(403)
            ->assertJsonPath('message', __('api.errors.unauthorized'));
    }

    public function test_admin_cannot_delete_unread_notification_but_can_delete_read_notification(): void
    {
        $admin = $this->makeAdminWithPermissions(['notifications.manage']);
        $this->actingAs($admin, 'admin');

        $unread = $admin->appNotifications()->create([
            'type' => 'custom_unread',
            'title' => 'Title',
            'message' => 'Unread',
            'read_at' => null,
        ]);

        $this->delete("/admin/notifications/{$unread->id}")
            ->assertStatus(302)
            ->assertSessionHas('error', __('admin.cannot_delete_unread_notification'));

        $this->assertDatabaseHas('app_notifications', ['id' => $unread->id]);

        $read = $admin->appNotifications()->create([
            'type' => 'custom_read',
            'title' => 'Title',
            'message' => 'Read',
            'read_at' => now(),
        ]);

        $this->delete("/admin/notifications/{$read->id}")
            ->assertStatus(302)
            ->assertSessionHas('success', __('admin.notification_deleted_successfully'));

        $this->assertDatabaseMissing('app_notifications', ['id' => $read->id]);
    }

    public function test_admin_cannot_mark_or_delete_notification_owned_by_other_admin(): void
    {
        $adminA = $this->makeAdminWithPermissions(['notifications.manage']);
        $adminB = $this->makeAdminWithPermissions(['notifications.manage']);

        $notificationForB = $adminB->appNotifications()->create([
            'type' => 'other_admin',
            'title' => 'Title',
            'message' => 'Message',
        ]);

        $this->actingAs($adminA, 'admin');

        $this->postJson("/admin/notifications/{$notificationForB->id}/read")->assertStatus(403);
        $this->deleteJson("/admin/notifications/{$notificationForB->id}")->assertStatus(403);
    }

    protected function makeAdminWithPermissions(array $permissions): Admin
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'admin');
        }

        $admin = Admin::query()->create([
            'name' => 'Notify Admin',
            'email' => 'notify-admin-'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $admin->givePermissionTo($permissions);

        return $admin;
    }
}
