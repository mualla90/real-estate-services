<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() &&
            auth('admin')->user()->can('admins.update');
    }

    public function rules(): array
    {
        /** @var \App\Models\Admin $admin */
        $admin = $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($admin?->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'admin')],
        ];
    }
}
