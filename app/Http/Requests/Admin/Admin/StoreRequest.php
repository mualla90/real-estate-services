<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() &&
            auth('admin')->user()->can('admins.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'role' => [
            'required',
            'string',
            Rule::exists('roles', 'name')
                ->where('guard_name', 'admin')
                ->whereNot('name', 'super_admin'),
                    ],
                ];
    }
}
