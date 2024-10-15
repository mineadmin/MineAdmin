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

use App\Schema\UserSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: UserSchema::class,
    only: [
        'nickname', 'password', 'avatar', 'phone', 'email', 'signed', 'backend_setting',
    ]
)]
class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|max:255',
            'signed' => 'sometimes|string|max:255',
            'backend_setting' => 'sometimes|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'nickname' => trans('user.nickname'),
            'password' => trans('user.password'),
            'avatar' => trans('user.avatar'),
            'phone' => trans('user.phone'),
            'email' => trans('user.email'),
            'signed' => trans('user.signed'),
            'backend_setting' => trans('user.backend_setting'),
        ];
    }
}
