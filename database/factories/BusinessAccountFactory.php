<?php

namespace Database\Factories;

use App\Models\ActivityType;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activity_type_id' => ActivityType::inRandomOrder()->value('id'),
            'city_id' => City::inRandomOrder()->value('id'),
            'license_number' => strtoupper($this->faker->unique()->bothify('LIC-###')),

            'name' => [
                'en' => $this->faker->company(),
                'ar' => 'شركة ' . $this->faker->word(),
            ],

            'description' => [
                'en' => $this->faker->sentence(),
                'ar' => 'وصف تجريبي',
            ],

            'phone' => $this->faker->numerify('09########'),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),

            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),

            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
