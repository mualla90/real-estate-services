<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminFcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        AdminFcmToken::query()->updateOrCreate(
            ['token' => $validated['token']],
            [
                'admin_id' => auth('admin')->id(),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'FCM token registered.',
        ]);
    }
}
