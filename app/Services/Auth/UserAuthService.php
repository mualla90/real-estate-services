<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthService
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'fcm_token' => $data['fcm_token'] ?? null,
            'is_active' => true,
        ]);

        $otp = $this->otpService->sendVerificationOtp($user);

        return [
            'user' => $user,
            'otp_code' => $otp->code, // temporary for testing only
        ];
    }

    public function login(array $data): array
    {
        $user = User::where('phone', $data['phone'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid credentials'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'phone' => ['Account inactive'],
            ]);
        }

        if (is_null($user->phone_verified_at)) {
            throw ValidationException::withMessages([
                'phone' => ['Phone number is not verified.'],
            ]);
        }

        $user->update([
            'last_login_at' => now(),
            'fcm_token' => $data['fcm_token'] ?? $user->fcm_token,
        ]);

        $token = $user->createToken('mobile')->accessToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->token()?->revoke();
    }
}
