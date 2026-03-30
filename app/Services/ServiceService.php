<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceService
{
    public function create(BusinessAccount $businessAccount, array $data): Service
    {
        return DB::transaction(function () use ($businessAccount, $data) {
            return Service::query()->create([
                'business_account_id' => $businessAccount->id,
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],
                'city_id' => $data['city_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'service_type' => $data['service_type'],
                'price' => $data['price'],
                'currency' => $data['currency'],
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_by_admin_id' => null,
                'reviewed_at' => null,
                'published_at' => null,
                'average_rating' => 0,
                'review_count' => 0,
                'views_count' => 0,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }

    public function update(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {
            $updateData = [
                'category_id' => $data['category_id'] ?? $service->category_id,
                'subcategory_id' => $data['subcategory_id'] ?? $service->subcategory_id,
                'city_id' => $data['city_id'] ?? $service->city_id,
                'title' => $data['title'] ?? $service->title,
                'description' => $data['description'] ?? $service->description,
                'service_type' => $data['service_type'] ?? $service->service_type,
                'price' => $data['price'] ?? $service->price,
                'currency' => $data['currency'] ?? $service->currency,
                'address' => $data['address'] ?? $service->address,
                'latitude' => $data['latitude'] ?? $service->latitude,
                'longitude' => $data['longitude'] ?? $service->longitude,
                'is_active' => $data['is_active'] ?? $service->is_active,
                'sort_order' => $data['sort_order'] ?? $service->sort_order,
            ];

            if ($service->status === 'approved') {
                $updateData['status'] = 'pending';
                $updateData['rejection_reason'] = null;
                $updateData['reviewed_by_admin_id'] = null;
                $updateData['reviewed_at'] = null;
                $updateData['published_at'] = null;
            }

            $service->update($updateData);

            return $service->fresh();
        });
    }

    public function approve(Service $service, Admin $admin): Service
    {
        return DB::transaction(function () use ($service, $admin) {
            if ($service->status !== 'pending') {
                abort(422, 'Only pending services can be approved.');
            }

            $service->update([
                'status' => 'approved',
                'rejection_reason' => null,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
                'published_at' => now(),
            ]);

            return $service->fresh();
        });
    }

    public function reject(Service $service, Admin $admin, string $rejectionReason): Service
    {
        return DB::transaction(function () use ($service, $admin, $rejectionReason) {
            if ($service->status !== 'pending') {
                abort(422, 'Only pending services can be rejected.');
            }

            $service->update([
                'status' => 'rejected',
                'rejection_reason' => $rejectionReason,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
                'published_at' => null,
            ]);

            return $service->fresh();
        });
    }

    public function delete(Service $service): void
    {
        DB::transaction(function () use ($service) {
            $service->delete();
        });
    }
}
