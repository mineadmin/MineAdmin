<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class BatchGrantRolesForUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role_codes' => 'present|array',
            'role_codes.*' => 'string|exists:role,code',
        ];
    }

    public function attributes(): array
    {
        return [
            'role_codes' => trans('role.code'),
        ];
    }
}
