<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BusinessAccountMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_account_can_upload_images_and_documents(): void
    {
        $user = User::factory()->create();
        $city = City::query()->create([
            'name' => ['en' => 'Damascus', 'ar' => 'Damascus'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $activityType = ActivityType::query()->create([
            'name' => ['en' => 'Supplier', 'ar' => 'Supplier'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Passport::actingAs($user);

        $response = $this->post('/api/business-accounts', [
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-MEDIA-1',
            'name' => ['en' => 'Media Business', 'ar' => 'Media Business'],
            'description' => ['en' => 'Desc', 'ar' => 'Desc'],
            'images' => [
                UploadedFile::fake()->createWithContent(
                    'office.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
                ),
            ],
            'documents' => [
                UploadedFile::fake()->create('license.pdf', 32, 'application/pdf'),
            ],
        ], ['Accept' => 'application/json']);

        $response->assertStatus(201);
        $response->assertJsonPath('data.status', 'pending');
        $response->assertJsonCount(1, 'data.images');
        $response->assertJsonCount(1, 'data.documents');
    }
}
