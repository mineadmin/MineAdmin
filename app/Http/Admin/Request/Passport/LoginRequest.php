<?php

namespace App\Http\Admin\Request\Passport;

use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use Hyperf\Validation\Request\FormRequest;

#[Schema(title: '登录请求', description: '登录请求参数',properties: [
    new Property('username', description: '用户名', type: 'string'),
    new Property('password', description: '密码', type: 'string'),
])]
class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'  =>  'required|string|exists:user,username',
            'password'  =>  'required|string',
        ];
    }

    public function attributes(): array
    {
        //
        return [
            'username'  =>  trans('user.username'),
            'password'  =>  trans('user.password'),
        ];
    }
}