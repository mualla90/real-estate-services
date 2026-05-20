<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppNotificationResource;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $user = $request->user();

        $notifications = $user->appNotifications()
            ->when($request->boolean('unread_only'), fn ($q) => $q->whereNull('read_at'))
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'message' => __('api.notifications.fetched'),
            'data' => AppNotificationResource::collection($notifications),
        ]);
    }

    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        $this->ensureOwnership($request, $notification);

        if (is_null($notification->read_at)) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'message' => __('api.notifications.marked_read'),
            'data' => new AppNotificationResource($notification->fresh()),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()
            ->appNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => __('api.notifications.marked_all_read'),
        ]);
    }

    protected function ensureOwnership(Request $request, AppNotification $notification): void
    {
        abort_if(
            $notification->notifiable_type !== get_class($request->user())
            || (int) $notification->notifiable_id !== (int) $request->user()->id,
            403,
            __('api.errors.unauthorized')
        );
    }
}
