<?php

namespace App\System\Request\Api;

use Hyperf\Validation\Request\FormRequest;

/**
 * 接口管理验证数据类 (Create)
 */
class SystemApiCreateRequest extends FormRequest
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
            
            // 接口名称 验证
            'name' => 'required',

            // 类名称 验证
            'class_name' => 'required',

            // 方法名 验证
            'method_name' => 'required',

            // 认证模式 (0简易 1复杂) 验证
            'auth_mode' => 'required',

            // 请求模式 (A 所有 P POST G GET) 验证
            'request_mode' => 'required',

        ];
    }
}