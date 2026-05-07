<?php

namespace App\Services\BusinessAccount;

use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\User;
use App\Services\Notification\NotificationService;

class BusinessAccountService
{
    public function __construct(
        protected NotificationService $notifications
    ) {
    }

    public function create(User $user, array $data): BusinessAccount
    {
        $businessAccount = BusinessAccount::create([
            'user_id' => $user->id,
            'activity_type_id' => $data['activity_type_id'],
            'city_id' => $data['city_id'],
            'license_number' => $data['license_number'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by_admin_id' => null,
            'reviewed_at' => null,
        ]);

        $this->attachMedia($businessAccount, $data);

        $this->notifications->notifyAdminsByPermission(
            'business-accounts.approve',
            'business_account_pending_review',
            __('api.notification_text.business_account_pending_review_title'),
            __('api.notification_text.business_account_pending_review_message'),
            [
                'business_account_id' => $businessAccount->id,
                'user_id' => $user->id,
            ]
        );

        return $businessAccount->fresh(['media', 'city', 'activityType']);
    }

    public function update(BusinessAccount $businessAccount, array $data): BusinessAccount
    {
        $payload = collect($data)
            ->except(['images', 'documents'])
            ->all();

        $businessAccount->update([
            ...$payload,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by_admin_id' => null,
            'reviewed_at' => null,
        ]);

        $this->attachMedia($businessAccount, $data);

        return $businessAccount->fresh(['media', 'city', 'activityType']);
    }

    public function approve(BusinessAccount $businessAccount, Admin $admin): BusinessAccount
    {
        $businessAccount->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $businessAccount = $businessAccount->fresh();

        if ($businessAccount->user) {
            $this->notifications->notifyUser(
                $businessAccount->user,
                'business_account_approved',
                __('api.notification_text.business_account_approved_title'),
                __('api.notification_text.business_account_approved_message'),
                [
                    'business_account_id' => $businessAccount->id,
                ]
            );
        }

        return $businessAccount;
    }

    public function reject(BusinessAccount $businessAccount, Admin $admin, string $reason): BusinessAccount
    {
        $businessAccount->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $businessAccount = $businessAccount->fresh();

        if ($businessAccount->user) {
            $this->notifications->notifyUser(
                $businessAccount->user,
                'business_account_rejected',
                __('api.notification_text.business_account_rejected_title'),
                __('api.notification_text.business_account_rejected_message'),
                [
                    'business_account_id' => $businessAccount->id,
                    'reason' => $reason,
                ]
            );
        }

        return $businessAccount;
    }

    protected function attachMedia(BusinessAccount $businessAccount, array $data): void
    {
        foreach ($data['images'] ?? [] as $image) {
            $businessAccount->addMedia($image)->toMediaCollection('images');
        }

        foreach ($data['documents'] ?? [] as $document) {
            $businessAccount->addMedia($document)->toMediaCollection('documents');
        }
    }
}
