<?php

namespace App\Http\Admin\Request\Passport;

use Hyperf\Validation\Request\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username'  =>  'required|string|exists:user,username',
            'password'  =>  'required|string',
        ];
    }
}