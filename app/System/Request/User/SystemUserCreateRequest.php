<?php

declare(strict_types=1);

namespace App\System\Request\User;

use Hyperf\Validation\Request\FormRequest;

class SystemUserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => 'required|max:20',
            'password' => 'required|min:6',
            'dept_id'  => 'required',
            'role_ids' => 'required'
        ];
    }
}
