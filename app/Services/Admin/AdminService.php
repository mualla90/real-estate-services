<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function create(array $data): Admin
    {
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (! empty($data['role'])) {
            $admin->syncRoles([$data['role']]);
        }

        return $admin->fresh();
    }

    public function update(Admin $admin, array $data): Admin
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $admin->is_active,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $admin->update($payload);

        if (! empty($data['role'])) {
            $admin->syncRoles([$data['role']]);
        }

        return $admin->fresh();
    }
    public function delete(Admin $admin): void
    {
        $admin->delete();
    }
}
