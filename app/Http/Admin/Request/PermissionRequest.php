<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /** 昵称 */
            'nickname' => 'sometimes|string|max:255',
            /** 新密码 */
            'new_password' => 'sometimes|confirmed|string|min:8',
            /** 确认新密码 */
            'new_password_confirmation' => 'sometimes|string|min:8',
            /** 原密码 */
            'old_password' => 'required_with:new_password|string',
            /** 头像 */
            'avatar' => 'sometimes|string|max:255',
            /** 个人签名 */
            'signed' => 'sometimes|string|max:255',
            /** 后台设置 */
            'backend_setting' => 'sometimes|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'nickname' => trans('user.nickname'),
            'new_password' => trans('user.password'),
            'new_password_confirmation' => trans('user.password_confirmation'),
            'old_password' => trans('user.old_password'),
            'avatar' => trans('user.avatar'),
            'signed' => trans('user.signed'),
            'backend_setting' => trans('user.backend_setting'),
        ];
    }
}
