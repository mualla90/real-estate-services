<?php

namespace App\Services\Subcategory;

use App\Models\Subcategory;

class SubcategoryService
{
    public function create(array $data): Subcategory
    {
        return Subcategory::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $subcategory->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'] ?? $subcategory->sort_order,
        ]);

        return $subcategory->fresh();
    }

    public function delete(Subcategory $subcategory): void
    {
        $subcategory->delete();
    }
}
