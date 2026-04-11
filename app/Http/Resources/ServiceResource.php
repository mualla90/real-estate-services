<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,

            'business_account_id' => $this->business_account_id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'city_id' => $this->city_id,

            'title' => $this->getTranslation('title', $locale),
            'title_translations' => $this->getTranslations('title'),

            'description' => $this->description
                ? $this->getTranslation('description', $locale)
                : null,
            'description_translations' => $this->description
                ? $this->getTranslations('description')
                : null,

            'service_type' => $this->service_type,
            'price' => $this->price,
            'currency' => $this->currency,

            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,

            'reviewed_by_admin_id' => $this->reviewed_by_admin_id,
            'reviewed_at' => $this->reviewed_at,
            'published_at' => $this->published_at,

            'average_rating' => $this->average_rating,
            'review_count' => $this->review_count,
            'views_count' => $this->views_count,

            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'is_favorited' => (bool) ($this->is_favorited ?? false),

            'main_image' => $this->getFirstMediaUrl('main_image') ?: null,
            'images' => $this->getMedia('gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->name,
                ];
            })->values(),

            'category' => $this->whenLoaded('category', function () use ($locale) {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->getTranslation('name', $locale),
                    'name_translations' => $this->category->getTranslations('name'),
                ];
            }),

            'subcategory' => $this->when(
                $this->relationLoaded('subcategory') && $this->subcategory,
                function () use ($locale) {
                return [
                    'id' => $this->subcategory->id,
                    'name' => $this->subcategory->getTranslation('name', $locale),
                    'name_translations' => $this->subcategory->getTranslations('name'),
                ];
            }),

            'city' => $this->whenLoaded('city', function () use ($locale) {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->getTranslation('name', $locale),
                    'name_translations' => $this->city->getTranslations('name'),
                ];
            }),

            'dynamic_fields' => $this->whenLoaded('dynamicFieldValues', function () use ($locale) {
                return $this->dynamicFieldValues->map(function ($item) use ($locale) {
                    return [
                        'dynamic_field_id' => $item->dynamic_field_id,
                        'field_key' => $item->dynamicField?->field_key,
                        'name' => $item->dynamicField?->name
                            ? $item->dynamicField->getTranslation('name', $locale)
                            : null,
                        'name_translations' => $item->dynamicField?->name
                            ? $item->dynamicField->getTranslations('name')
                            : null,
                        'field_type' => $item->field_type,
                        'value_text' => $item->value_text,
                        'value_number' => $item->value_number,
                        'value_json' => $item->value_json,
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
