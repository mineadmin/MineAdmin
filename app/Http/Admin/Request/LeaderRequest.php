<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class LeaderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|array',
            'user_id.*' => 'integer|exists:user,id',
            'dept_id' => 'required|integer|exists:department,id',
        ];
    }
}
