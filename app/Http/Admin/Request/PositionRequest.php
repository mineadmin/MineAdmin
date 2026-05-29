<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PositionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('position', 'name')->ignore($this->route('id')),
            ],
            'dept_id' => 'required|integer|exists:department,id',
        ];
    }
}
