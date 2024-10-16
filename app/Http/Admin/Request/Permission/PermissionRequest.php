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

use App\Http\CurrentUser;
use App\Schema\UserSchema;
use App\Service\Permission\UserService;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: UserSchema::class,
    only: [
        'nickname', 'password', 'avatar', 'signed', 'backend_setting',
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
            'newPassword' => 'sometimes|confirmed|string',
            'newPassword_confirmation' => 'sometimes|string',
            'oldPassword' => ['sometimes', function ($attribute, $value, $fail) {
                $user = $this->container->get(CurrentUser::class);
                $service = $this->container->get(UserService::class);

                $model = $service->getInfo($user->id());
                if (! $model->verifyPassword($value)) {
                    $fail('旧密码错误');
                }
            }],
            'avatar' => 'sometimes|string|max:255',
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
            'signed' => trans('user.signed'),
            'backend_setting' => trans('user.backend_setting'),
        ];
    }
}
