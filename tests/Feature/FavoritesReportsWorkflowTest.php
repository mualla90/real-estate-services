<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\Favorite;
use App\Models\Report;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FavoritesReportsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_list_and_remove_favorite(): void
    {
        [$ownerUser, $ownerBusiness, $service] = $this->createFavoriteReportContext();
        Passport::actingAs($ownerUser);

        $add = $this->postJson("/api/business-accounts/{$ownerBusiness->id}/favorites", [
            'service_id' => $service->id,
            'note' => 'Interesting service',
        ]);
        $add->assertStatus(201);
        $add->assertJsonPath('message', __('api.favorites.added'));

        $this->assertDatabaseHas('favorites', [
            'business_account_id' => $ownerBusiness->id,
            'service_id' => $service->id,
            'note' => 'Interesting service',
        ]);

        $list = $this->getJson("/api/business-accounts/{$ownerBusiness->id}/favorites");
        $list->assertOk();
        $list->assertJsonPath('message', __('api.favorites.fetched'));

        $remove = $this->deleteJson("/api/business-accounts/{$ownerBusiness->id}/favorites/{$service->id}");
        $remove->assertOk();
        $remove->assertJsonPath('message', __('api.favorites.removed'));

        $this->assertSame(
            0,
            Favorite::query()
                ->where('business_account_id', $ownerBusiness->id)
                ->where('service_id', $service->id)
                ->count()
        );
    }

    public function test_cannot_manage_favorites_for_another_business_account(): void
    {
        [$ownerUser, $ownerBusiness, $service, $otherBusiness] = $this->createFavoriteReportContext(withOtherBusiness: true);
        Passport::actingAs($ownerUser);

        $response = $this->postJson("/api/business-accounts/{$otherBusiness->id}/favorites", [
            'service_id' => $service->id,
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('message', __('api.errors.unauthorized'));
    }

    public function test_can_submit_report_for_visible_service(): void
    {
        [$ownerUser, $ownerBusiness, $service] = $this->createFavoriteReportContext();
        Passport::actingAs($ownerUser);

        $response = $this->postJson("/api/business-accounts/{$ownerBusiness->id}/reports/services/{$service->id}", [
            'reason' => 'Spam content',
            'description' => 'Looks invalid.',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('message', __('api.reports.submitted'));

        $this->assertDatabaseHas('reports', [
            'reporter_business_account_id' => $ownerBusiness->id,
            'reportable_type' => Service::class,
            'reportable_id' => $service->id,
            'reason' => 'Spam content',
            'status' => 'pending',
        ]);
    }

    public function test_cannot_submit_report_for_invisible_service(): void
    {
        [$ownerUser, $ownerBusiness, $service] = $this->createFavoriteReportContext();
        $service->update([
            'status' => 'pending',
            'published_at' => null,
        ]);

        Passport::actingAs($ownerUser);
        $response = $this->postJson("/api/business-accounts/{$ownerBusiness->id}/reports/services/{$service->id}", [
            'reason' => 'Spam content',
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, Report::query()->count());
    }

    protected function createFavoriteReportContext(bool $withOtherBusiness = false): array
    {
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
            'license_number' => 'LIC-FAV-1',
            'name' => ['en' => 'Owner BA', 'ar' => 'حساب المالك'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0911111111',
            'email' => 'owner-fav@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        $providerUser = User::factory()->create();
        $providerBusiness = BusinessAccount::query()->create([
            'user_id' => $providerUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-FAV-2',
            'name' => ['en' => 'Provider BA', 'ar' => 'حساب المزود'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0922222222',
            'email' => 'provider-fav@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        $service = Service::query()->create([
            'business_account_id' => $providerBusiness->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Visible Service', 'ar' => 'خدمة ظاهرة'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'service_type' => 'sale',
            'price' => 450,
            'currency' => 'USD',
            'status' => 'approved',
            'published_at' => now(),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        if (! $withOtherBusiness) {
            return [$ownerUser, $ownerBusiness, $service];
        }

        $otherUser = User::factory()->create();
        $otherBusiness = BusinessAccount::query()->create([
            'user_id' => $otherUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-FAV-3',
            'name' => ['en' => 'Other BA', 'ar' => 'حساب آخر'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0933333333',
            'email' => 'other-fav@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        return [$ownerUser, $ownerBusiness, $service, $otherBusiness];
    }
}

