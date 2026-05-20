<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'activity_type_id' => $this->activity_type_id,
            'city_id' => $this->city_id,
            'license_number' => $this->license_number,
            'name' => $this->getTranslation('name', $locale),
            'name_translations' => $this->getTranslations('name'),
            'description' => $this->description
                ? $this->getTranslation('description', $locale)
                : null,
            'description_translations' => $this->description
                ? $this->getTranslations('description')
                : null,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'reviewed_by_admin_id' => $this->reviewed_by_admin_id,
            'reviewed_at' => $this->reviewed_at,
            'images' => $this->getMedia('images')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
            ])->values(),
            'documents' => $this->getMedia('documents')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'mime_type' => $media->mime_type,
            ])->values(),
            'city' => $this->whenLoaded('city', function () use ($locale) {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->getTranslation('name', $locale),
                    'name_translations' => $this->city->getTranslations('name'),
                ];
            }),
            'activity_type' => $this->whenLoaded('activityType', function () use ($locale) {
                return [
                    'id' => $this->activityType->id,
                    'name' => $this->activityType->getTranslation('name', $locale),
                    'name_translations' => $this->activityType->getTranslations('name'),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
