<?php

namespace App\Http\Admin\Request;

use App\Http\Common\Request\ClientOsTrait;
use Illuminate\Foundation\Http\FormRequest;

class PassportLoginRequest extends FormRequest
{
    use ClientOsTrait;

    public function rules(): array
    {
        return [
            /** 账号 */
            'username' => 'required|string|exists:user,username',
            /** 密码 */
            'password' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => trans('user.username'),
            'password' => trans('user.password'),
        ];
    }
}
