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
use App\Schema\MenuSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: MenuSchema::class,
    only: [
        'parent_id', 'name', 'code', 'icon', 'route', 'component', 'redirect',
        'is_hidden', 'type', 'status', 'sort', 'remark',
    ]
)]
class MenuRequest extends FormRequest
{
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'parent_id' => 'sometimes|integer',
            'name' => 'required|string|max:255',
            'path' => 'sometimes|string|max:255',
            'component' => 'sometimes|string|max:255',
            'redirect' => 'sometimes|string|max:255',
            'status' => 'sometimes|integer',
            'sort' => 'sometimes|integer',
            'remark' => 'sometimes|string|max:255',
            'meta.title' => 'required|string|max:255',
            'meta.i18n' => 'sometimes|string|max:255',
            'meta.badge' => 'sometimes|string|max:255',
            'meta.link' => 'sometimes|string|max:255',
            'meta.icon' => 'sometimes|string|max:255',
            'meta.affix' => 'sometimes|boolean',
            'meta.hidden' => 'sometimes|boolean',
            'meta.type' => 'sometimes|string|max:255',
            'meta.cache' => 'sometimes|boolean',
            'meta.breadcrumbEnable' => 'sometimes|boolean',
            'meta.copyright' => 'sometimes|boolean',
            'meta.componentPath' => 'sometimes|string|max:64',
            'meta.componentSuffix' => 'sometimes|string|max:4',
            'meta.activeName' => 'sometimes|string|max:255',
            'btnPermission' => 'sometimes|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => trans('menu.parent_id'),
            'name' => trans('menu.name'),
            'component' => trans('menu.component'),
            'redirect' => trans('menu.redirect'),
            'path' => trans('menu.path'),
            'is_hidden' => trans('menu.is_hidden'),
            'status' => trans('menu.status'),
            'sort' => trans('menu.sort'),
            'remark' => trans('menu.remark'),
            'meta.title' => trans('menu.meta.title'),
            'meta.i18n' => trans('menu.meta.i18n'),
            'meta.badge' => trans('menu.meta.badge'),
            'meta.icon' => trans('menu.meta.icon'),
            'meta.affix' => trans('menu.meta.affix'),
            'meta.hidden' => trans('menu.meta.hidden'),
            'meta.type' => trans('menu.meta.type'),
            'meta.cache' => trans('menu.meta.cache'),
            'meta.link' => trans('menu.meta.link'),
            'meta.activeName' => trans('menu.meta.activeName'),
        ];
    }
}
