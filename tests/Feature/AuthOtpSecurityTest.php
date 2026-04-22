<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthOtpSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_response_does_not_expose_otp_code(): void
    {
        config(['services.ultramsg.enabled' => false]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'OTP User',
            'phone' => '0994111000',
            'email' => 'otp-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonMissingPath('data.otp_code');
        $response->assertJsonMissingPath('data.user.fcm_token');
        $response->assertJsonMissingPath('data.user.from_token');
    }

    public function test_resend_otp_response_does_not_expose_otp_code(): void
    {
        config(['services.ultramsg.enabled' => false]);

        User::query()->create([
            'name' => 'Resend User',
            'phone' => '0994222000',
            'email' => 'resend-user@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/resend-otp', [
            'phone' => '0994222000',
        ]);

        $response->assertOk();
        $response->assertJsonMissingPath('data.otp_code');
    }
}
