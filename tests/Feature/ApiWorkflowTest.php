<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\DynamicField;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_business_account_cannot_create_service(): void
    {
        [$user, $businessAccount, $category, $subcategory, $city] = $this->createOwnerContext('pending');
        Passport::actingAs($user);

        $response = $this->postJson("/api/business-accounts/{$businessAccount->id}/services", [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Test service', 'ar' => 'خدمة تجريبية'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'service_type' => 'sale',
            'price_usd' => 1000,
            'price_syp' => 13000000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', __('api.errors.business_account_not_approved'));
    }

    public function test_updating_approved_service_moves_it_back_to_pending(): void
    {
        [$user, $businessAccount, $category, $subcategory, $city] = $this->createOwnerContext('approved');

        $service = Service::query()->create([
            'business_account_id' => $businessAccount->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Original', 'ar' => 'أصلي'],
            'description' => ['en' => 'Original', 'ar' => 'أصلي'],
            'service_type' => 'sale',
            'price' => 100,
            'currency' => 'USD',
            'price_usd' => 100,
            'price_syp' => 1300000,
            'status' => 'approved',
            'published_at' => now(),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Passport::actingAs($user);
        $response = $this->putJson("/api/business-accounts/{$businessAccount->id}/services/{$service->id}", [
            'price_usd' => 250,
            'price_syp' => 3250000,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', __('api.services.updated'));
        $this->assertSame('pending', $service->fresh()->status);
    }

    public function test_review_requires_accepted_service_request(): void
    {
        [$requesterUser, $requesterBusiness, $providerBusiness, $service] = $this->createRequestFlowContext();

        $serviceRequest = ServiceRequest::query()->create([
            'service_id' => $service->id,
            'requester_business_account_id' => $requesterBusiness->id,
            'provider_business_account_id' => $providerBusiness->id,
            'status' => 'pending',
            'quantity' => 1,
        ]);

        Passport::actingAs($requesterUser);
        $response = $this->postJson(
            "/api/business-accounts/{$requesterBusiness->id}/service-requests/{$serviceRequest->id}/reviews",
            [
                'rating' => 5,
                'comment' => 'Great service',
            ]
        );

        $response->assertStatus(422);
        $this->assertSame(0, $service->reviews()->count());
    }

    public function test_service_request_can_be_reviewed_only_once(): void
    {
        [$requesterUser, $requesterBusiness, $providerBusiness, $service] = $this->createRequestFlowContext();

        $serviceRequest = ServiceRequest::query()->create([
            'service_id' => $service->id,
            'requester_business_account_id' => $requesterBusiness->id,
            'provider_business_account_id' => $providerBusiness->id,
            'status' => 'accepted',
            'quantity' => 1,
        ]);

        Passport::actingAs($requesterUser);

        $first = $this->postJson(
            "/api/business-accounts/{$requesterBusiness->id}/service-requests/{$serviceRequest->id}/reviews",
            [
                'rating' => 4,
                'comment' => 'Good',
            ]
        );

        $first->assertStatus(201);
        $first->assertJsonPath('message', __('api.reviews.created'));

        $second = $this->postJson(
            "/api/business-accounts/{$requesterBusiness->id}/service-requests/{$serviceRequest->id}/reviews",
            [
                'rating' => 5,
                'comment' => 'Duplicate',
            ]
        );

        $second->assertStatus(422);
        $this->assertSame(1, $service->reviews()->count());
    }

    public function test_service_requires_required_dynamic_fields(): void
    {
        [$user, $businessAccount, $category, $subcategory, $city] = $this->createOwnerContext('approved');

        DynamicField::query()->create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => ['en' => 'Area', 'ar' => 'Area'],
            'field_key' => 'area',
            'field_type' => 'number',
            'is_required' => true,
            'status' => 'active',
        ]);

        Passport::actingAs($user);

        $response = $this->postJson("/api/business-accounts/{$businessAccount->id}/services", [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Test service', 'ar' => 'Test service'],
            'description' => ['en' => 'Desc', 'ar' => 'Desc'],
            'service_type' => 'sale',
            'price_usd' => 1000,
            'price_syp' => 13000000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['dynamic_fields']);
    }

    public function test_service_requires_both_usd_and_syp_prices(): void
    {
        [$user, $businessAccount, $category, $subcategory, $city] = $this->createOwnerContext('approved');

        Passport::actingAs($user);

        $response = $this->postJson("/api/business-accounts/{$businessAccount->id}/services", [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Test service', 'ar' => 'Test service'],
            'description' => ['en' => 'Desc', 'ar' => 'Desc'],
            'service_type' => 'sale',
            'price_usd' => 1000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price_syp']);
    }

    protected function createOwnerContext(string $businessStatus): array
    {
        $user = User::factory()->create();
        $city = City::query()->create([
            'name' => ['en' => 'Damascus', 'ar' => 'دمشق'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $activityType = ActivityType::query()->create([
            'name' => ['en' => 'Supplier', 'ar' => 'مورد'],
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

        $businessAccount = BusinessAccount::query()->create([
            'user_id' => $user->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-100',
            'name' => ['en' => 'BA 1', 'ar' => 'حساب 1'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0999999999',
            'email' => 'ba1@example.com',
            'address' => 'Address',
            'status' => $businessStatus,
        ]);

        return [$user, $businessAccount, $category, $subcategory, $city];
    }

    protected function createRequestFlowContext(): array
    {
        [$requesterUser, $requesterBusiness, $category, $subcategory, $city] = $this->createOwnerContext('approved');
        [, $providerBusiness] = $this->createSecondApprovedBusiness($city);

        $service = Service::query()->create([
            'business_account_id' => $providerBusiness->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Provider Service', 'ar' => 'خدمة مقدم'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'service_type' => 'sale',
            'price' => 150,
            'currency' => 'USD',
            'price_usd' => 150,
            'price_syp' => 1950000,
            'status' => 'approved',
            'published_at' => now(),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        return [$requesterUser, $requesterBusiness, $providerBusiness, $service];
    }

    protected function createSecondApprovedBusiness(City $city): array
    {
        $user = User::factory()->create();
        $activityType = ActivityType::query()->firstOrCreate(
            ['sort_order' => 2],
            ['name' => ['en' => 'Broker', 'ar' => 'وسيط'], 'is_active' => true]
        );

        $business = BusinessAccount::query()->create([
            'user_id' => $user->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-200',
            'name' => ['en' => 'BA 2', 'ar' => 'حساب 2'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0988888888',
            'email' => 'ba2@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        return [$user, $business];
    }
}
