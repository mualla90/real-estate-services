<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        // Create 5 users
        User::factory(5)->create()->each(function ($user) use ($admin) {

            // Each user gets 1–3 business accounts
            BusinessAccount::factory(rand(1, 3))
                ->create([
                    'user_id' => $user->id,
                ])
                ->each(function ($account) use ($admin) {

                    if ($account->status !== 'pending') {
                        $account->update([
                            'reviewed_by_admin_id' => $admin?->id,
                            'reviewed_at' => now(),
                            'rejection_reason' => $account->status === 'rejected'
                                ? 'Sample rejection reason'
                                : null,
                        ]);
                    }
                });
        });
    }
}
