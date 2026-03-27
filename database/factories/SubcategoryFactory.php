<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubcategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->value('id'),

            'name' => [
                'en' => $this->faker->unique()->word(),
                'ar' => 'فرعي ' . $this->faker->word(),
            ],

            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
