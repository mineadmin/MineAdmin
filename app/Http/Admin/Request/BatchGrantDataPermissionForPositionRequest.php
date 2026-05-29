<?php

namespace App\Http\Admin\Request;

use App\Models\Enums\DataPermission\PolicyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchGrantDataPermissionForPositionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'policy_type' => ['required', 'string', Rule::enum(PolicyType::class)],
            'value' => 'sometimes|array|min:1',
        ];
    }
}
