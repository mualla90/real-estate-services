<?php

namespace App\Http\Requests\Admin\DynamicField;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $dynamicField = $this->route('dynamicField');

        return [
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],

            'field_key' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('dynamic_fields', 'field_key')->ignore($dynamicField?->id),
            ],
            'field_type' => ['required', Rule::in(['text', 'number', 'select', 'boolean', 'date'])],

            'category_id' => ['nullable', 'exists:categories,id', 'required_without:subcategory_id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id', 'required_without:category_id'],

            'is_required' => ['nullable', 'boolean'],
            'options_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }
}

