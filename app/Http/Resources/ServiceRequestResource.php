<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'requester_business_account_id' => $this->requester_business_account_id,
            'provider_business_account_id' => $this->provider_business_account_id,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'needed_at' => $this->needed_at,
            'message' => $this->message,
            'price_offer' => $this->price_offer,
            'rejection_reason' => $this->rejection_reason,
            'responded_at' => $this->responded_at,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at,

            'service' => new ServiceResource($this->whenLoaded('service')),

            'requester_business_account' => $this->whenLoaded('requesterBusinessAccount', function () {
                $locale = app()->getLocale();

                return [
                    'id' => $this->requesterBusinessAccount->id,
                    'name' => $this->requesterBusinessAccount->getTranslation('name', $locale),
                    'name_translations' => $this->requesterBusinessAccount->getTranslations('name'),
                ];
            }),

            'provider_business_account' => $this->whenLoaded('providerBusinessAccount', function () {
                $locale = app()->getLocale();

                return [
                    'id' => $this->providerBusinessAccount->id,
                    'name' => $this->providerBusinessAccount->getTranslation('name', $locale),
                    'name_translations' => $this->providerBusinessAccount->getTranslations('name'),
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

