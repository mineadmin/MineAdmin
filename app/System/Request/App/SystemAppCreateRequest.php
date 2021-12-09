<?php

namespace App\System\Request\App;

use Hyperf\Validation\Request\FormRequest;

/**
 * 应用管理验证数据类 (Create)
 */
class SystemAppCreateRequest extends FormRequest
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
            
            // 应用分组 验证
            'group_id' => 'required',

            // 应用名称 验证
            'app_name' => 'required',

            // APP ID 验证
            'app_id' => 'required',

            // APP SECRET 验证
            'app_secret' => 'required',

        ];
    }
}