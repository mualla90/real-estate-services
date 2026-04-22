<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'phone' => '0994000111',
            'email' => 'old@example.com',
        ]);

        Passport::actingAs($user);

        $response = $this->putJson('/api/auth/profile', [
            'name' => 'Updated Name',
            'phone' => '0994000222',
            'email' => 'new@example.com',
            'latitude' => 33.5,
            'longitude' => 36.3,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', __('api.auth.profile_updated'));
        $response->assertJsonPath('data.name', 'Updated Name');
        $response->assertJsonPath('data.phone', '0994000222');
        $response->assertJsonPath('data.email', 'new@example.com');
    }

    public function test_profile_update_rejects_duplicate_phone(): void
    {
        $user = User::factory()->create(['phone' => '0994555000']);
        User::factory()->create(['phone' => '0994666000']);

        Passport::actingAs($user);

        $response = $this->putJson('/api/auth/profile', [
            'phone' => '0994666000',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }
}

