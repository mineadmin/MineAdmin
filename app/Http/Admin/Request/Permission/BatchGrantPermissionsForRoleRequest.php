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
    title: '批量授权角色权限',
    properties: [
        new Property('permission_ids', description: '权限ID', type: 'array', example: '[1,2,3]'),
    ]
)]
class BatchGrantPermissionsForRoleRequest extends FormRequest
{
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string|exists:menu,name',
        ];
    }

    public function attributes(): array
    {
        return [
            'permissions' => trans('menu.name'),
        ];
    }
}
