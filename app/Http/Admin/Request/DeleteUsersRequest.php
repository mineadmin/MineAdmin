<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUsersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            '*' => 'integer|exists:user,id',
        ];
    }
}
