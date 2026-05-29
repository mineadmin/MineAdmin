<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class BatchGrantPermissionsForRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /** 权限 */
            'permissions' => 'sometimes|array',
            /** 权限标识 */
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
