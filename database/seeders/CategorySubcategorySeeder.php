<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySubcategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 categories
        $categories = Category::factory(5)->create();

        // Each category gets 2–4 subcategories
        foreach ($categories as $category) {
            Subcategory::factory(rand(2, 4))->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
