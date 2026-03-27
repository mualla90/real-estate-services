<?php

namespace App\Services\Category;

use App\Models\Category;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
