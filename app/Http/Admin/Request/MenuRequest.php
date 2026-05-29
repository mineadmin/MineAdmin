<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /** 父级菜单 */
            'parent_id' => 'sometimes|integer',
            /** 菜单标识 */
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menu', 'name')->ignore($this->route('id')),
            ],
            /** 路由地址 */
            'path' => 'sometimes|nullable|string|max:255',
            /** 组件路径 */
            'component' => 'sometimes|nullable|string|max:255',
            /** 重定向地址 */
            'redirect' => 'sometimes|nullable|string|max:255',
            /** 状态 */
            'status' => 'sometimes|integer|in:1,2',
            /** 排序 */
            'sort' => 'sometimes|integer',
            /** 备注 */
            'remark' => 'sometimes|nullable|string|max:255',
            /** 菜单标题 */
            'meta.title' => 'required|string|max:255',
            /** 国际化标识 */
            'meta.i18n' => 'sometimes|string|max:255',
            /** 徽标 */
            'meta.badge' => 'sometimes|string|max:255',
            /** 外链 */
            'meta.link' => 'sometimes|string|max:255',
            /** 图标 */
            'meta.icon' => 'sometimes|string|max:255',
            /** 是否固定 */
            'meta.affix' => 'sometimes|boolean',
            /** 是否隐藏 */
            'meta.hidden' => 'sometimes|boolean',
            /** 菜单类型 */
            'meta.type' => 'sometimes|string|max:255',
            /** 是否缓存 */
            'meta.cache' => 'sometimes|boolean',
            /** 是否启用面包屑 */
            'meta.breadcrumbEnable' => 'sometimes|boolean',
            /** 版权 */
            'meta.copyright' => 'sometimes|boolean',
            /** 是否使用默认布局 */
            'meta.useDefaultLayout' => 'sometimes|boolean',
            /** 组件真实路径 */
            'meta.componentPath' => 'sometimes|string|max:64',
            /** 组件后缀 */
            'meta.componentSuffix' => 'sometimes|string|max:4',
            /** 激活菜单 */
            'meta.activeName' => 'sometimes|string|max:255',
            /** 按钮权限 */
            'btnPermission' => 'sometimes|array',
            /** 按钮权限ID */
            'btnPermission.*.id' => 'sometimes|integer|exists:menu,id',
            /** 按钮权限类型 */
            'btnPermission.*.type' => 'sometimes|string|max:255',
            /** 按钮权限标识 */
            'btnPermission.*.code' => 'required_with:btnPermission|string|max:255',
            /** 按钮权限标题 */
            'btnPermission.*.title' => 'required_with:btnPermission|string|max:255',
            /** 按钮权限国际化标识 */
            'btnPermission.*.i18n' => 'required_with:btnPermission|string|max:255',
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
            'status' => trans('menu.status'),
            'sort' => trans('menu.sort'),
            'remark' => trans('menu.remark'),
            'meta.title' => trans('menu.meta.title'),
            'meta.i18n' => trans('menu.meta.i18n'),
            'meta.badge' => trans('menu.meta.badge'),
            'meta.icon' => trans('menu.meta.icon'),
            'meta.affix' => trans('menu.meta.affix'),
            'meta.hidden' => trans('menu.meta.hidden'),
            'meta.copyright' => trans('menu.meta.copyright'),
            'meta.useDefaultLayout' => trans('menu.meta.useDefaultLayout'),
            'meta.type' => trans('menu.meta.type'),
            'meta.cache' => trans('menu.meta.cache'),
            'meta.link' => trans('menu.meta.link'),
            'meta.activeName' => trans('menu.meta.activeName'),
        ];
    }
}
