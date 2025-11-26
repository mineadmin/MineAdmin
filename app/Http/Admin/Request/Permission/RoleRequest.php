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
use App\Schema\RoleSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: RoleSchema::class,
    only: [
        'name', 'code', 'status', 'sort', 'remark', 'policy',
    ]
)]
class RoleRequest extends FormRequest
{
    use HttpMethodTrait;
    use NoAuthorizeTrait;

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:60',
            'code' => [
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'status' => 'sometimes|integer|in:1,2',
            'sort' => 'required|integer',
            'remark' => 'nullable|string|max:255',
        ];
        if ($this->isCreate()) {
            $rules['code'][] = 'unique:role,code';
        }
        if ($this->isUpdate()) {
            $rules['code'][] = 'unique:role,code,' . $this->route('id');
        }
        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => trans('role.name'),
            'code' => trans('role.code'),
            'status' => trans('role.status'),
            'sort' => trans('role.sort'),
            'remark' => trans('role.remark'),
        ];
    }
}
