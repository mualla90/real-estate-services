<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminReportModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_reports_manage_cannot_update_report_status(): void
    {
        [$admin, $report] = $this->createReportContext(['reports.view']);
        $this->actingAs($admin, 'admin');

        $this->patchJson("/admin/reports/{$report->id}/status", [
            'status' => 'resolved',
        ])->assertForbidden();
    }

    public function test_admin_with_reports_manage_can_update_report_status_and_review_fields(): void
    {
        [$admin, $report] = $this->createReportContext(['reports.view', 'reports.manage']);
        $this->actingAs($admin, 'admin');

        $this->patch("/admin/reports/{$report->id}/status", [
            'status' => 'resolved',
        ])->assertStatus(302);

        $report->refresh();
        $this->assertSame('resolved', $report->status);
        $this->assertSame($admin->id, $report->reviewed_by_admin_id);
        $this->assertNotNull($report->reviewed_at);
    }

    protected function createReportContext(array $permissions): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'admin');
        }

        $admin = Admin::query()->create([
            'name' => 'Report Admin',
            'email' => 'report-admin-'.uniqid().'@example.com',
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

        $ownerUser = User::factory()->create();
        $ownerBusiness = BusinessAccount::query()->create([
            'user_id' => $ownerUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-RPT-1',
            'name' => ['en' => 'Owner BA', 'ar' => 'حساب المالك'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0911111111',
            'email' => 'owner-rpt@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        $reporterUser = User::factory()->create();
        $reporterBusiness = BusinessAccount::query()->create([
            'user_id' => $reporterUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-RPT-2',
            'name' => ['en' => 'Reporter BA', 'ar' => 'حساب المبلّغ'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0922222222',
            'email' => 'reporter-rpt@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        $service = Service::query()->create([
            'business_account_id' => $ownerBusiness->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Reported Service', 'ar' => 'خدمة مبلّغ عنها'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'service_type' => 'sale',
            'price' => 500,
            'currency' => 'USD',
            'status' => 'approved',
            'published_at' => now(),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $report = Report::query()->create([
            'reporter_business_account_id' => $reporterBusiness->id,
            'reportable_type' => Service::class,
            'reportable_id' => $service->id,
            'reason' => 'Spam',
            'description' => 'Suspicious listing',
            'status' => 'pending',
        ]);

        return [$admin, $report];
    }
}
