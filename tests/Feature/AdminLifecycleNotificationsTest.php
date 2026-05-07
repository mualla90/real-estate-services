<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminLifecycleNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_account_approve_and_reject_update_status_and_notify_owner(): void
    {
        [$admin, $businessAccount] = $this->createBusinessAccountContext(['business-accounts.approve', 'business-accounts.reject']);
        $this->actingAs($admin, 'admin');

        $this->patch("/admin/business-accounts/{$businessAccount->id}/approve")->assertStatus(302);
        $businessAccount->refresh();
        $this->assertSame('approved', $businessAccount->status);
        $this->assertSame($admin->id, $businessAccount->reviewed_by_admin_id);

        $this->assertDatabaseHas('app_notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $businessAccount->user_id,
            'type' => 'business_account_approved',
        ]);

        $businessAccount->update([
            'status' => 'pending',
            'rejection_reason' => null,
        ]);

        $this->patch("/admin/business-accounts/{$businessAccount->id}/reject", [
            'rejection_reason' => 'Incomplete documents',
        ])->assertStatus(302);

        $businessAccount->refresh();
        $this->assertSame('rejected', $businessAccount->status);
        $this->assertSame('Incomplete documents', $businessAccount->rejection_reason);

        $this->assertDatabaseHas('app_notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $businessAccount->user_id,
            'type' => 'business_account_rejected',
        ]);
    }

    public function test_service_approve_reject_activate_deactivate_and_notify_owner(): void
    {
        [$admin, $service, $ownerUser] = $this->createServiceContext([
            'services.approve',
            'services.reject',
            'services.activate',
            'services.deactivate',
        ]);
        $this->actingAs($admin, 'admin');

        $this->post("/admin/services/{$service->id}/approve")->assertStatus(302);
        $service->refresh();
        $this->assertSame('approved', $service->status);
        $this->assertNotNull($service->published_at);
        $this->assertDatabaseHas('app_notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $ownerUser->id,
            'type' => 'service_approved',
        ]);

        $this->patch("/admin/services/{$service->id}/deactivate")->assertStatus(302);
        $this->assertFalse((bool) $service->fresh()->is_active);

        $this->patch("/admin/services/{$service->id}/activate")->assertStatus(302);
        $this->assertTrue((bool) $service->fresh()->is_active);

        $service->update([
            'status' => 'pending',
            'published_at' => null,
            'rejection_reason' => null,
        ]);

        $this->post("/admin/services/{$service->id}/reject", [
            'rejection_reason' => 'Policy violation',
        ])->assertStatus(302);
        $service->refresh();
        $this->assertSame('rejected', $service->status);
        $this->assertSame('Policy violation', $service->rejection_reason);

        $this->assertDatabaseHas('app_notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $ownerUser->id,
            'type' => 'service_rejected',
        ]);
    }

    protected function createBusinessAccountContext(array $permissions): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'admin');
        }

        $admin = Admin::query()->create([
            'name' => 'Lifecycle Admin',
            'email' => 'lifecycle-admin-'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $admin->givePermissionTo($permissions);

        $city = City::query()->create([
            'name' => ['en' => 'Damascus', 'ar' => 'دمشق'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $activityType = ActivityType::query()->create([
            'name' => ['en' => 'Broker', 'ar' => 'وسيط'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $ownerUser = User::factory()->create();
        $businessAccount = BusinessAccount::query()->create([
            'user_id' => $ownerUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-LIFE-BA',
            'name' => ['en' => 'Owner BA', 'ar' => 'حساب المالك'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0931111111',
            'email' => 'owner-life-ba@example.com',
            'address' => 'Address',
            'status' => 'pending',
        ]);

        return [$admin, $businessAccount];
    }

    protected function createServiceContext(array $permissions): array
    {
        [$admin, $businessAccount] = $this->createBusinessAccountContext($permissions);

        $category = Category::query()->create([
            'name' => ['en' => 'Real Estate', 'ar' => 'عقارات'],
            'description' => ['en' => 'Main', 'ar' => 'رئيسي'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $subcategory = Subcategory::query()->create([
            'category_id' => $category->id,
            'name' => ['en' => 'Apartments', 'ar' => 'شقق'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $service = Service::query()->create([
            'business_account_id' => $businessAccount->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $businessAccount->city_id,
            'title' => ['en' => 'Pending Service', 'ar' => 'خدمة معلقة'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'service_type' => 'sale',
            'price' => 700,
            'currency' => 'USD',
            'price_usd' => 700,
            'price_syp' => 9100000,
            'status' => 'pending',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $ownerUser = $businessAccount->user;

        return [$admin, $service, $ownerUser];
    }
}
