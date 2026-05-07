<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function cities(): JsonResponse
    {
        $locale = app()->getLocale();

        $cities = City::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->getTranslation('name', $locale),
                'name_translations' => $city->getTranslations('name'),
                'sort_order' => $city->sort_order,
            ])
            ->values();

        return response()->json([
            'message' => __('api.cities.fetched'),
            'data' => $cities,
        ]);
    }

    public function activityTypes(): JsonResponse
    {
        $locale = app()->getLocale();

        $activityTypes = ActivityType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ActivityType $activityType) => [
                'id' => $activityType->id,
                'name' => $activityType->getTranslation('name', $locale),
                'name_translations' => $activityType->getTranslations('name'),
                'sort_order' => $activityType->sort_order,
            ])
            ->values();

        return response()->json([
            'message' => __('api.activity_types.fetched'),
            'data' => $activityTypes,
        ]);
    }
}
