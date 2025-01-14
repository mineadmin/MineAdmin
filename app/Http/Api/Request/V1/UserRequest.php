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

namespace App\Http\Api\Request\V1;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use App\Schema\UserSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: UserSchema::class,
    only: [
        'username', 'password',
    ]
)]
class UserRequest extends FormRequest
{
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:16',
            'password' => 'required|string|max:32',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => trans('user.username'),
            'password' => trans('user.password'),
        ];
    }
}
