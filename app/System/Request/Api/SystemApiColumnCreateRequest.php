<?php

namespace App\System\Request\Api;

use Hyperf\Validation\Request\FormRequest;

/**
 * 接口验证数据类 (Create)
 */
class SystemApiColumnCreateRequest extends FormRequest
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

            // 字段名称 验证
            'name' => 'required',

            'api_id' => 'required',

            // 字段类型 0 请求 1 返回 验证
            'type' => 'required',

            // 数据类型 验证
            'data_type' => 'required',

            // 是否必填 0 非必填 1 必填 验证
            'is_required' => 'required',

        ];
    }
}