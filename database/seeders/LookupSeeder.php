<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\ActivityType;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        // Cities
        $cities = [
            ['en' => 'Damascus', 'ar' => 'دمشق'],
            ['en' => 'Aleppo', 'ar' => 'حلب'],
            ['en' => 'Homs', 'ar' => 'حمص'],
        ];

        foreach ($cities as $city) {
            City::create([
                'name' => $city,
            ]);
        }

        // Activity Types
        $activities = [
            ['en' => 'Real Estate Office', 'ar' => 'مكتب عقاري'],
            ['en' => 'Construction Company', 'ar' => 'شركة مقاولات'],
        ];

        foreach ($activities as $activity) {
            ActivityType::create([
                'name' => $activity,
            ]);
        }
    }
}
