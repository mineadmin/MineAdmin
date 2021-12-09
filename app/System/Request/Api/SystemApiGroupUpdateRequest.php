<?php

namespace App\System\Request\Api;

use Hyperf\Validation\Request\FormRequest;

/**
 * 接口分组验证数据类 (Update)
 */
class SystemApiGroupUpdateRequest extends FormRequest
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
            // 接口组名称 验证
            'name' => 'required',
        ];
    }
}