<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /** 角色名称 */
            'name' => 'required|string|max:60',
            /** 角色代码 */
            'code' => [
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('role', 'code')->ignore($this->route('id')),
            ],
            /** 状态 */
            'status' => 'sometimes|integer|in:1,2',
            /** 排序 */
            'sort' => 'required|integer',
            /** 备注 */
            'remark' => 'nullable|string|max:255',
        ];
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
