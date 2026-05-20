<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'initiator_business_account_id' => $this->initiator_business_account_id,
            'recipient_business_account_id' => $this->recipient_business_account_id,
            'last_message_at' => $this->last_message_at,
            'unread_messages_count' => (int) ($this->unread_messages_count ?? 0),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'initiator_business_account' => $this->whenLoaded('initiatorBusinessAccount', function () use ($locale) {
                return [
                    'id' => $this->initiatorBusinessAccount->id,
                    'name' => $this->initiatorBusinessAccount->getTranslation('name', $locale),
                ];
            }),
            'recipient_business_account' => $this->whenLoaded('recipientBusinessAccount', function () use ($locale) {
                return [
                    'id' => $this->recipientBusinessAccount->id,
                    'name' => $this->recipientBusinessAccount->getTranslation('name', $locale),
                ];
            }),
            'latest_message' => new MessageResource($this->whenLoaded('latestMessage')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

