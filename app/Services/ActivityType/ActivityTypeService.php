<?php

namespace App\Services\ActivityType;

use App\Models\ActivityType;

class ActivityTypeService
{
    public function create(array $data): ActivityType
    {
        return ActivityType::create([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(ActivityType $activityType, array $data): ActivityType
    {
        $activityType->update([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? $activityType->status,
            'sort_order' => $data['sort_order'] ?? $activityType->sort_order,
        ]);

        return $activityType->fresh();
    }

    public function delete(ActivityType $activityType): void
    {
        $activityType->delete();
    }
}
