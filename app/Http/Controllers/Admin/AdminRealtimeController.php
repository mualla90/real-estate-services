<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pusher\Pusher;

class AdminRealtimeController extends Controller
{
    public function pusherAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'socket_id' => ['required', 'string'],
            'channel_name' => ['required', 'string'],
        ]);

        abort_unless(
            $validated['channel_name'] === 'private-admin.'.auth('admin')->id(),
            403,
            __('admin.unauthorized')
        );

        $pusher = new Pusher(
            (string) config('broadcasting.connections.pusher.key'),
            (string) config('broadcasting.connections.pusher.secret'),
            (string) config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        return response()->json(json_decode(
            $pusher->authorizeChannel($validated['channel_name'], $validated['socket_id']),
            true
        ));
    }
}
