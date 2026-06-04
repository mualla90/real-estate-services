<?php

namespace App\Services\Subcategory;

use App\Models\Subcategory;
use App\Services\Notification\NotificationService;

class SubcategoryService
{
    public function __construct(
        protected NotificationService $notifications
    ) {
    }

    public function create(array $data): Subcategory
    {
        $subcategory = Subcategory::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->notifications->notifyAdminsByPermission(
            'subcategories.view',
            'subcategory_created',
            __('admin.subcategory_created_notification_title'),
            __('admin.subcategory_created_notification_message', [
                'name' => $subcategory->getTranslation('name', app()->getLocale(), false) ?: $subcategory->id,
            ]),
            [
                'subcategory_id' => $subcategory->id,
                'category_id' => $subcategory->category_id,
                'admin_id' => auth('admin')->id(),
            ]
        );

        return $subcategory;
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $subcategory->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? $subcategory->sort_order,
        ]);

        $subcategory = $subcategory->fresh();

        $this->notifications->notifyAdminsByPermission(
            'subcategories.view',
            'subcategory_updated',
            __('admin.subcategory_updated_notification_title'),
            __('admin.subcategory_updated_notification_message', [
                'name' => $subcategory->getTranslation('name', app()->getLocale(), false) ?: $subcategory->id,
            ]),
            [
                'subcategory_id' => $subcategory->id,
                'category_id' => $subcategory->category_id,
                'admin_id' => auth('admin')->id(),
            ]
        );

        return $subcategory;
    }

    public function delete(Subcategory $subcategory): void
    {
        $subcategory->delete();
    }
}
