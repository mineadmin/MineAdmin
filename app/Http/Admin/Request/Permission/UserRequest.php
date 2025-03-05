<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Http\Admin\Request\Permission;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use App\Model\Enums\DataPermission\PolicyType;
use App\Schema\UserSchema;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use Mine\Swagger\Attributes\FormRequest as FormRequestAnnotation;

#[FormRequestAnnotation(
    schema: UserSchema::class,
    title: '创建用户',
    required: [
        'username',
        'user_type',
        'nickname',
        'phone',
        'email',
        'avatar',
        'signed',
        'status',
        'backend_setting',
        'remark',
    ],
    only: [
        'username',
        'user_type',
        'nickname',
        'phone',
        'email',
        'avatar',
        'signed',
        'status',
        'backend_setting',
        'remark',
        'policy',
    ]
)]
class UserRequest extends FormRequest
{
    use NoAuthorizeTrait;

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
            'password' => 'sometimes|string|min:6|max:20',
            'policy' => 'sometimes|array',
            'policy.*.policy_type' => [
                'required_with:policy',
                'string',
                'max:10',
                Rule::enum(PolicyType::class),
            ],
            'policy.*.value' => [
                'sometimes',
                'array',
                'min:1',
            ],
            'policy.*.is_default' => [
                'sometimes',
                'integer',
                'in:0,1',
            ],
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
            'password' => trans('user.password'),
        ];
    }
}
