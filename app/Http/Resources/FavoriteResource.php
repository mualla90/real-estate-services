<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_account_id' => $this->business_account_id,
            'service_id' => $this->service_id,
            'note' => $this->note,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

