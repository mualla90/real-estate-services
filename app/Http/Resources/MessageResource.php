<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_business_account_id' => $this->sender_business_account_id,
            'body' => $this->body,
            'status' => $this->status,
            'read_at' => $this->read_at,
            'sender_business_account' => $this->whenLoaded('senderBusinessAccount', function () use ($locale) {
                return [
                    'id' => $this->senderBusinessAccount->id,
                    'name' => $this->senderBusinessAccount->getTranslation('name', $locale),
                    'name_translations' => $this->senderBusinessAccount->getTranslations('name'),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

