<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:60',
                Rule::unique('department', 'name')->ignore($this->route('id')),
            ],
            'parent_id' => 'sometimes|integer',
            'department_users' => 'sometimes|array',
            'department_users.*' => 'integer|exists:user,id',
            'leader' => 'sometimes|array',
            'leader.*' => 'integer|exists:user,id',
        ];
    }
}
