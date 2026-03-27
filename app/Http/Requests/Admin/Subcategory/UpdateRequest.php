<?php

namespace App\Http\Requests\Admin\Subcategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() &&
            auth('admin')->user()->can('subcategories.update');
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
