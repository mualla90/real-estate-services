<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $admin = auth('admin')->user();

        $notifications = $admin->appNotifications()
            ->when($request->boolean('unread_only'), fn ($q) => $q->whereNull('read_at'))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'deepLinkResolver' => fn (AppNotification $notification) => $this->resolveDeepLink($notification),
        ]);
    }

    public function markRead(AppNotification $notification): RedirectResponse
    {
        $this->ensureOwnership($notification);

        if (is_null($notification->read_at)) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', __('admin.notification_marked_read'));
    }

    public function markAllRead(): RedirectResponse
    {
        auth('admin')->user()
            ->appNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', __('admin.notifications_marked_read_all'));
    }

    public function destroy(AppNotification $notification): RedirectResponse
    {
        $this->ensureOwnership($notification);

        if (is_null($notification->read_at)) {
            return redirect()->back()->with('error', __('admin.cannot_delete_unread_notification'));
        }

        $notification->delete();

        return redirect()->back()->with('success', __('admin.notification_deleted_successfully'));
    }

    protected function ensureOwnership(AppNotification $notification): void
    {
        $admin = auth('admin')->user();

        abort_if(
            $notification->notifiable_type !== get_class($admin)
            || (int) $notification->notifiable_id !== (int) $admin->id,
            403,
            __('admin.unauthorized')
        );
    }

    protected function resolveDeepLink(AppNotification $notification): ?string
    {
        $data = $notification->data ?? [];

        return match ($notification->type) {
            'business_account_pending_review',
            'business_account_approved',
            'business_account_rejected' => ! empty($data['business_account_id'])
                ? route('admin.business-accounts.show', ['businessAccount' => $data['business_account_id']])
                : route('admin.business-accounts.index'),

            'service_pending_review',
            'service_approved',
            'service_rejected' => ! empty($data['service_id'])
                ? route('admin.services.review.show', ['service' => $data['service_id']])
                : route('admin.services.review.index'),

            default => null,
        };
    }
}
