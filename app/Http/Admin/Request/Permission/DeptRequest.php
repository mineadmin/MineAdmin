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

use App\Kernel\Swagger\Attributes\FormRequest as FormRequestAnnotation;
use App\Schema\DeptSchema;
use Hyperf\Validation\Request\FormRequest;

#[FormRequestAnnotation(
    schema: DeptSchema::class,
    only: [
        'parent_id',
        'name',
        'leader',
        'phone',
        'status',
        'sort',
        'remark',
    ]
)]
class DeptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'required|integer',
            'name' => 'required|string|max:60',
            'leader' => 'required|string|max:60',
            'phone' => 'required|string|max:12',
            'status' => 'required|integer',
            'sort' => 'required|integer',
            'remark' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => trans('dept.parent_id'),
            'name' => trans('dept.name'),
            'leader' => trans('dept.leader'),
            'phone' => trans('dept.phone'),
            'status' => trans('dept.status'),
            'sort' => trans('dept.sort'),
            'remark' => trans('dept.remark'),
        ];
    }
}
