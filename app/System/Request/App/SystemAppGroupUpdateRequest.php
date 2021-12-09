<?php

namespace App\System\Request\App;

use Hyperf\Validation\Request\FormRequest;

/**
 * 应用分组验证数据类 (Update)
 */
class SystemAppGroupUpdateRequest extends FormRequest
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
            
            // 应用组名称 验证
            'name' => 'required',

        ];
    }
}