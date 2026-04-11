<?php

namespace App\Http\Requests\Api\Service;

use App\Rules\DynamicFieldMatchesServiceContext;
use App\Rules\SubcategoryBelongsToCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        $service = $this->route('service');
        $categoryId = $this->input('category_id', $service?->category_id);
        $subcategoryId = array_key_exists('subcategory_id', $this->all())
            ? $this->input('subcategory_id')
            : $service?->subcategory_id;

        return [
            'category_id' => [
                'sometimes',
                'required',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'subcategory_id' => [
                'sometimes',
                'nullable',
                Rule::exists('subcategories', 'id')->where('is_active', true),
                new SubcategoryBelongsToCategory($categoryId),
            ],
            'city_id' => [
                'sometimes',
                'required',
                Rule::exists('cities', 'id')->where('is_active', true),
            ],

            'title' => ['sometimes', 'required', 'array'],
            'title.en' => ['required_with:title', 'string', 'max:255'],
            'title.ar' => ['required_with:title', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],

            'service_type' => ['sometimes', 'required', Rule::in(['sale', 'rent'])],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'required', 'string', 'max:10'],

            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'main_image' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'images' => ['sometimes', 'nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:5120'],

            'dynamic_fields' => ['sometimes', 'nullable', 'array'],
            'dynamic_fields.*.dynamic_field_id' => [
                'required_with:dynamic_fields',
                'integer',
                'exists:dynamic_fields,id',
                new DynamicFieldMatchesServiceContext($categoryId, $subcategoryId),
            ],
            'dynamic_fields.*.value' => ['nullable'],

            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
