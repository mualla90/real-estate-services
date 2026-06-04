<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Services\Notification\NotificationService;

class CategoryService
{
    public function __construct(
        protected NotificationService $notifications
    ) {
    }

    public function create(array $data): Category
    {
        $category = Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->notifications->notifyAdminsByPermission(
            'categories.view',
            'category_created',
            __('admin.category_created_notification_title'),
            __('admin.category_created_notification_message', [
                'name' => $category->getTranslation('name', app()->getLocale(), false) ?: $category->id,
            ]),
            [
                'category_id' => $category->id,
                'admin_id' => auth('admin')->id(),
            ]
        );

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        $category = $category->fresh();

        $this->notifications->notifyAdminsByPermission(
            'categories.view',
            'category_updated',
            __('admin.category_updated_notification_title'),
            __('admin.category_updated_notification_message', [
                'name' => $category->getTranslation('name', app()->getLocale(), false) ?: $category->id,
            ]),
            [
                'category_id' => $category->id,
                'admin_id' => auth('admin')->id(),
            ]
        );

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
