<?php

declare(strict_types=1);

namespace App\System\Request\Role;

use Hyperf\Validation\Request\FormRequest;

class SystemRoleStatusRequest extends FormRequest
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
            'id' => 'required',
            'status' => 'required'
        ];
    }
}
