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
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use Hyperf\Validation\Request\FormRequest;

#[Schema(
    title: '批量授权用户角色',
    properties: [
        new Property('role_ids', description: '角色ID', type: 'array', example: '[1,2,3]'),
    ]
)]
class BatchGrantRolesForUserRequest extends FormRequest
{
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'role_codes' => 'required|array',
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
