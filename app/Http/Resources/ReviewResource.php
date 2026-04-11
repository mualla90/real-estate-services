<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'service_request_id' => $this->service_request_id,
            'reviewer_business_account_id' => $this->reviewer_business_account_id,
            'rating' => $this->rating,
            'comment' => $this->comment,

            'reviewer_business_account' => $this->whenLoaded('reviewerBusinessAccount', function () use ($locale) {
                return [
                    'id' => $this->reviewerBusinessAccount->id,
                    'name' => $this->reviewerBusinessAccount->getTranslation('name', $locale),
                    'name_translations' => $this->reviewerBusinessAccount->getTranslations('name'),
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

