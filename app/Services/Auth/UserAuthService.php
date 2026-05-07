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

        $this->otpService->sendVerificationOtp($user);

        return [
            'user' => $user,
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

        if (array_key_exists('fcm_token', $data)) {
            $user->update([
                'fcm_token' => $data['fcm_token'],
            ]);
        }

        $this->otpService->sendLoginOtp($user);

        return [
            'user' => $user,
        ];
    }

    public function issueToken(User $user): array
    {
        $user->update([
            'last_login_at' => now(),
        ]);

        return [
            'user' => $user->fresh(),
            'token' => $user->createToken('mobile')->accessToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->token()?->revoke();
    }

    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }
}
