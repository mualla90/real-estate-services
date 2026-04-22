<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResendOtpRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Services\Auth\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected UserAuthService $service,
        protected OtpService $otpService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->service->register($request->validated());

        return response()->json([
            'message' => __('api.auth.registered_otp_sent'),
            'data' => $data,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->service->login($request->validated());

        return response()->json([
            'message' => __('api.auth.login_successful'),
            'data' => $data,
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = $this->otpService->verifyOtp(
            $request->phone,
            $request->code
        );

        return response()->json([
            'message' => __('api.auth.phone_verified'),
            'data' => $user,
        ]);
    }

    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->firstOrFail();

        $this->otpService->resendOtp($user);

        return response()->json([
            'message' => __('api.auth.otp_resent'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json([
            'message' => __('api.auth.logged_out'),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->service->updateProfile($request->user(), $request->validated());

        return response()->json([
            'message' => __('api.auth.profile_updated'),
            'data' => $user,
        ]);
    }
}
