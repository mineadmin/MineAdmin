<?php

namespace App\Http\Admin\Request;

use App\Models\Enums\DataPermission\PolicyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:20',
            'user_type' => 'required|integer',
            'nickname' => ['required', 'string', 'max:60', 'regex:/^[^\s]+$/'],
            'phone' => 'sometimes|string|max:12',
            'email' => 'sometimes|string|max:60|email:rfc,dns',
            'avatar' => 'sometimes|string|max:255|url',
            'signed' => 'sometimes|string|max:255',
            'status' => 'sometimes|integer',
            'backend_setting' => 'sometimes|array|max:255',
            'remark' => 'sometimes|string|max:255',
            'policy' => 'sometimes|array',
            'policy.policy_type' => [
                'required_with:policy',
                'string',
                'max:20',
                Rule::enum(PolicyType::class),
            ],
            'policy.value' => 'sometimes',
            'department' => 'sometimes|array',
            'department.*' => 'required_with:department|integer|exists:department,id',
            'position' => 'sometimes|array',
            'position.*' => 'sometimes|integer|exists:position,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => trans('user.username'),
            'user_type' => trans('user.user_type'),
            'nickname' => trans('user.nickname'),
            'phone' => trans('user.phone'),
            'email' => trans('user.email'),
            'avatar' => trans('user.avatar'),
            'signed' => trans('user.signed'),
            'status' => trans('user.status'),
            'backend_setting' => trans('user.backend_setting'),
            'created_by' => trans('user.created_by'),
            'remark' => trans('user.remark'),
            'department' => trans('user.department'),
        ];
    }
}
