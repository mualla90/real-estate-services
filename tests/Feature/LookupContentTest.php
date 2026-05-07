<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LookupContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_lookup_endpoints_return_active_cities_and_activity_types(): void
    {
        City::query()->create([
            'name' => ['en' => 'Damascus', 'ar' => 'Damascus'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        ActivityType::query()->create([
            'name' => ['en' => 'Broker', 'ar' => 'Broker'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->getJson('/api/cities')
            ->assertOk()
            ->assertJsonPath('message', __('api.cities.fetched'))
            ->assertJsonPath('data.0.name', 'Damascus');

        $this->getJson('/api/activity-types')
            ->assertOk()
            ->assertJsonPath('message', __('api.activity_types.fetched'))
            ->assertJsonPath('data.0.name', 'Broker');
    }

    public function test_content_pages_are_available_for_settings(): void
    {
        $this->getJson('/api/privacy-policy')
            ->assertOk()
            ->assertJsonPath('data.key', 'privacy_policy');

        $this->getJson('/api/terms-of-use')
            ->assertOk()
            ->assertJsonPath('data.key', 'terms_of_use');
    }
}
