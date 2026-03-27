<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->unique()->word(),
                'ar' => 'فئة ' . $this->faker->word(),
            ],

            'description' => [
                'en' => $this->faker->sentence(),
                'ar' => 'وصف تجريبي',
            ],

            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
