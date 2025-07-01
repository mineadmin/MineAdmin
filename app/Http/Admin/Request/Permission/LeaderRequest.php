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

use App\Http\Common\Request\Traits\HttpMethodTrait;
use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use App\Schema\LeaderSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: LeaderSchema::class,
    only: [
        'dept_id', 'user_id',
    ]
)]
class LeaderRequest extends FormRequest
{
    use HttpMethodTrait;
    use NoAuthorizeTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|array',
            'dept_id' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => '用户ID',
            'dept_id' => '部门ID',
        ];
    }
}
