<?php

namespace App\Http\Requests\Api\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        $businessAccount = $this->route('businessAccount');

        return [
            'activity_type_id' => ['sometimes', 'exists:activity_types,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'license_number' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('business_accounts', 'license_number')->ignore($businessAccount?->id),
            ],

            'name' => ['sometimes', 'array'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'name.ar' => ['required_with:name', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],

            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],

            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
