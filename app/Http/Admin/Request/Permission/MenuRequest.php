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

use App\Schema\MenuSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Kernel\Swagger\Attributes\FormRequest(
    schema: MenuSchema::class,
    only: [
        'parent_id', 'name', 'code', 'icon', 'route', 'component', 'redirect',
        'is_hidden', 'type', 'status', 'sort', 'remark',
    ]
)]
class MenuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'sometimes|integer',
            'name' => 'required|string|max:255',
            'path' => 'required|string|max:255',
            'component' => 'sometimes|string|max:255',
            'redirect' => 'sometimes|string|max:255',
            'status' => 'sometimes|integer',
            'sort' => 'sometimes|integer',
            'remark' => 'sometimes|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => trans('menu.parent_id'),
            'name' => trans('menu.name'),
            'component' => trans('menu.component'),
            'redirect' => trans('menu.redirect'),
            'is_hidden' => trans('menu.is_hidden'),
            'status' => trans('menu.status'),
            'sort' => trans('menu.sort'),
            'remark' => trans('menu.remark'),
        ];
    }
}
