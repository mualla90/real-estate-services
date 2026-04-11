<?php

namespace App\Http\Requests\Api\Favorite;

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
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}

