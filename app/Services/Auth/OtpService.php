<?php

namespace App\Services\Auth;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\WhatsApp\UltraMsgService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class OtpService
{
    public function __construct(
        protected UltraMsgService $ultraMsgService
    ) {
    }

    public function sendVerificationOtp(User $user): OtpCode
    {
        return $this->sendOtp($user, 'verification');
    }

    public function sendLoginOtp(User $user): OtpCode
    {
        return $this->sendOtp($user, 'login');
    }

    public function sendOtp(User $user, string $type): OtpCode
    {
        OtpCode::where('phone', $user->phone)
            ->where('type', $type)
            ->whereNull('used_at')
            ->whereNull('verified_at')
            ->delete();

        $otp = OtpCode::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'code' => (string) random_int(100000, 999999),
            'type' => $type,
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            $this->ultraMsgService->sendOtp($user, $otp->code);
        } catch (Throwable $e) {
            Log::error('Failed to send OTP via UltraMsg.', [
                'user_id' => $user->id,
                'phone' => $user->phone,
                'error' => $e->getMessage(),
            ]);

            if (! config('services.ultramsg.fail_silently', true)) {
                throw ValidationException::withMessages([
                    'phone' => ['Unable to send OTP right now. Please try again.'],
                ]);
            }
        }

        return $otp;
    }

    public function verifyOtp(string $phone, string $code, string $type = 'verification'): User
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('type', $type)
            ->latest()
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => ['Invalid OTP code.'],
            ]);
        }

        if ($otp->isUsed()) {
            throw ValidationException::withMessages([
                'code' => ['This OTP has already been used.'],
            ]);
        }

        if ($otp->isExpired()) {
            throw ValidationException::withMessages([
                'code' => ['This OTP has expired.'],
            ]);
        }

        $otp->update([
            'verified_at' => now(),
            'used_at' => now(),
        ]);

        $user = $otp->user;

        if ($type === 'verification') {
            $user->update([
                'phone_verified_at' => now(),
            ]);
        }

        return $user;
    }

    public function resendOtp(User $user): OtpCode
    {
        return $this->sendVerificationOtp($user);
    }
}
