<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale),
            'title_translations' => $this->getTranslations('title'),
            'subtitle' => $this->subtitle
                ? $this->getTranslation('subtitle', $locale)
                : null,
            'subtitle_translations' => $this->subtitle
                ? $this->getTranslations('subtitle')
                : null,
            'link' => $this->link,
            'image' => $this->getFirstMediaUrl('image') ?: null,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'created_at' => $this->created_at,
        ];
    }
}

