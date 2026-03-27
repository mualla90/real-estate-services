<?php

namespace App\Services\City;

use App\Models\City;

class CityService
{
    public function create(array $data): City
    {
        return City::create([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(City $city, array $data): City
    {
        $city->update([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return $city->fresh();
    }

    public function delete(City $city): void
    {
        $city->delete();
    }
}
