<?php

namespace App\Http\Requests\Api\Service;

use App\Rules\DynamicFieldMatchesServiceContext;
use App\Rules\SubcategoryBelongsToCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'subcategory_id' => [
                'nullable',
                Rule::exists('subcategories', 'id')->where('is_active', true),
                new SubcategoryBelongsToCategory($this->input('category_id')),
            ],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where('is_active', true),
            ],

            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],

            'service_type' => ['required', Rule::in(['sale', 'rent'])],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],

            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'main_image' => ['nullable', 'image', 'max:5120'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:5120'],

            'dynamic_fields' => ['nullable', 'array'],
            'dynamic_fields.*.dynamic_field_id' => [
                'required_with:dynamic_fields',
                'integer',
                'exists:dynamic_fields,id',
                new DynamicFieldMatchesServiceContext(
                    $this->input('category_id'),
                    $this->input('subcategory_id')
                ),
            ],
            'dynamic_fields.*.value' => ['nullable'],

            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
