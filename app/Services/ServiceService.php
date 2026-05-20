<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\DynamicField;
use App\Models\Service;
use App\Models\ServiceDynamicFieldValue;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;

class ServiceService
{
    public function __construct(
        protected NotificationService $notifications
    ) {
    }

    public function create(BusinessAccount $businessAccount, array $data): Service
    {
        $service = DB::transaction(function () use ($businessAccount, $data) {
            $service = Service::query()->create([
                'business_account_id' => $businessAccount->id,
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'] ?? null,
                'city_id' => $data['city_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'service_type' => $data['service_type'],
                'price' => $data['price_usd'],
                'currency' => 'USD',
                'price_usd' => $data['price_usd'],
                'price_syp' => $data['price_syp'],
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

            if (! empty($data['main_image'])) {
                $service
                    ->addMedia($data['main_image'])
                    ->toMediaCollection('main_image');
            }

            if (! empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $service->addMedia($image)->toMediaCollection('gallery');
                }
            }

            $this->syncDynamicFields($service, $data['dynamic_fields'] ?? []);

            return $service->load([
                'category',
                'subcategory',
                'city',
                'media',
                'dynamicFieldValues.dynamicField',
            ]);
        });

        $this->notifications->notifyAdminsByPermission(
            'services.approve',
            'service_pending_review',
            __('api.notification_text.service_pending_review_title'),
            __('api.notification_text.service_pending_review_message'),
            [
                'service_id' => $service->id,
                'business_account_id' => $service->business_account_id,
            ]
        );

        return $service;
    }

    public function update(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {
            $updateData = [
                'category_id' => $data['category_id'] ?? $service->category_id,
                'subcategory_id' => array_key_exists('subcategory_id', $data)
                    ? $data['subcategory_id']
                    : $service->subcategory_id,
                'city_id' => $data['city_id'] ?? $service->city_id,
                'title' => $data['title'] ?? $service->title,
                'description' => $data['description'] ?? $service->description,
                'service_type' => $data['service_type'] ?? $service->service_type,
                'price' => $data['price_usd'] ?? $service->price,
                'currency' => 'USD',
                'price_usd' => $data['price_usd'] ?? $service->price_usd,
                'price_syp' => $data['price_syp'] ?? $service->price_syp,
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

            if (array_key_exists('main_image', $data) && $data['main_image']) {
                $service
                    ->addMedia($data['main_image'])
                    ->toMediaCollection('main_image');
            }

            if (! empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $service->addMedia($image)->toMediaCollection('gallery');
                }
            }

            if (array_key_exists('dynamic_fields', $data)) {
                $this->syncDynamicFields($service, $data['dynamic_fields'] ?? []);
            }

            return $service->fresh()->load([
                'category',
                'subcategory',
                'city',
                'media',
                'dynamicFieldValues.dynamicField',
            ]);
        });
    }

    public function approve(Service $service, Admin $admin): Service
    {
        $service = DB::transaction(function () use ($service, $admin) {
            if ($service->status !== 'pending') {
                abort(422, __('api.errors.only_pending_services_can_be_approved'));
            }

            $service->update([
                'status' => 'approved',
                'rejection_reason' => null,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
                'published_at' => now(),
            ]);

            return $service->fresh()->load([
                'category',
                'subcategory',
                'city',
                'media',
                'dynamicFieldValues.dynamicField',
            ]);
        });

        $owner = $service->businessAccount?->user;
        if ($owner) {
            $this->notifications->notifyUser(
                $owner,
                'service_approved',
                __('api.notification_text.service_approved_title'),
                __('api.notification_text.service_approved_message'),
                [
                    'service_id' => $service->id,
                    'business_account_id' => $service->business_account_id,
                ]
            );
        }

        return $service;
    }

    public function reject(Service $service, Admin $admin, string $rejectionReason): Service
    {
        $service = DB::transaction(function () use ($service, $admin, $rejectionReason) {
            if ($service->status !== 'pending') {
                abort(422, __('api.errors.only_pending_services_can_be_rejected'));
            }

            $service->update([
                'status' => 'rejected',
                'rejection_reason' => $rejectionReason,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
                'published_at' => null,
            ]);

            return $service->fresh()->load([
                'category',
                'subcategory',
                'city',
                'media',
                'dynamicFieldValues.dynamicField',
            ]);
        });

        $owner = $service->businessAccount?->user;
        if ($owner) {
            $this->notifications->notifyUser(
                $owner,
                'service_rejected',
                __('api.notification_text.service_rejected_title'),
                __('api.notification_text.service_rejected_message'),
                [
                    'service_id' => $service->id,
                    'reason' => $rejectionReason,
                ]
            );
        }

        return $service;
    }

    public function delete(Service $service): void
    {
        DB::transaction(function () use ($service) {
            $service->delete();
        });
    }
    public function activate(Service $service): Service
    {
        return DB::transaction(function () use ($service) {
            $service->update([
                'is_active' => true,
            ]);

            return $service->fresh()->load([
                'category',
                'subcategory',
                'city',
                'media',
                'dynamicFieldValues.dynamicField',
            ]);
        });
    }

    public function deactivate(Service $service): Service
    {
        return DB::transaction(function () use ($service) {
            $service->update([
                'is_active' => false,
            ]);

            return $service->fresh()->load([
                'category',
                'subcategory',
                'city',
                'media',
                'dynamicFieldValues.dynamicField',
            ]);
        });
    }

    protected function syncDynamicFields(Service $service, array $dynamicFields): void
    {
        if ($dynamicFields === []) {
            return;
        }

        $fieldIds = collect($dynamicFields)
            ->pluck('dynamic_field_id')
            ->filter()
            ->values();

        $fields = DynamicField::query()
            ->whereIn('id', $fieldIds)
            ->get()
            ->keyBy('id');

        foreach ($dynamicFields as $item) {
            $fieldId = $item['dynamic_field_id'] ?? null;
            if (! $fieldId || ! $fields->has($fieldId)) {
                continue;
            }

            $field = $fields[$fieldId];
            $value = $item['value'] ?? null;

            ServiceDynamicFieldValue::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'dynamic_field_id' => $field->id,
                ],
                [
                    'field_type' => $field->field_type,
                    'value_text' => is_scalar($value) ? (string) $value : null,
                    'value_number' => is_numeric($value) ? (float) $value : null,
                    'value_json' => is_array($value) ? $value : null,
                ]
            );
        }
    }
}
