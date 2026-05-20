<?php

namespace App\Http\Requests\Api\ServiceRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'needed_at' => ['nullable', 'date'],
            'message' => ['nullable', 'string', 'max:2000'],
            'price_offer' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

