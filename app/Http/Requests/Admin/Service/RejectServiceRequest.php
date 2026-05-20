<?php

namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

class RejectServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
       return auth('admin')->check()
            && auth('admin')->user()->can('services.reject');
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ];
    }
}
