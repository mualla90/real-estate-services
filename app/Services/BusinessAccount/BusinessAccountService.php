<?php

namespace App\Services\BusinessAccount;

use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\User;

class BusinessAccountService
{
    public function create(User $user, array $data): BusinessAccount
    {
        return BusinessAccount::create([
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
    }

    public function update(BusinessAccount $businessAccount, array $data): BusinessAccount
    {
        $businessAccount->update([
            ...$data,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by_admin_id' => null,
            'reviewed_at' => null,
        ]);

        return $businessAccount->fresh();
    }

    public function approve(BusinessAccount $businessAccount, Admin $admin): BusinessAccount
    {
        $businessAccount->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $businessAccount->fresh();
    }

    public function reject(BusinessAccount $businessAccount, Admin $admin, string $reason): BusinessAccount
    {
        $businessAccount->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $businessAccount->fresh();
    }
}
