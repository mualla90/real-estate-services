<?php

namespace Tests\Feature;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
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

    public function test_login_requires_otp_before_token_is_issued(): void
    {
        config(['services.ultramsg.enabled' => false]);

        User::query()->create([
            'name' => 'Login User',
            'phone' => '0994333000',
            'email' => 'login-user@example.com',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        $login = $this->postJson('/api/auth/login', [
            'phone' => '0994333000',
            'password' => 'password123',
        ]);

        $login->assertOk();
        $login->assertJsonPath('message', __('api.auth.login_otp_sent'));
        $login->assertJsonMissingPath('data.token');

        $otp = OtpCode::query()
            ->where('phone', '0994333000')
            ->where('type', 'login')
            ->latest()
            ->firstOrFail();

        Passport::client()->factory()->asPersonalAccessTokenClient()->create([
            'provider' => 'users',
        ]);

        $verify = $this->postJson('/api/auth/verify-otp', [
            'phone' => '0994333000',
            'code' => $otp->code,
            'type' => 'login',
        ]);

        $verify->assertOk();
        $verify->assertJsonPath('message', __('api.auth.login_successful'));
        $verify->assertJsonStructure(['data' => ['user', 'token']]);
    }
}
