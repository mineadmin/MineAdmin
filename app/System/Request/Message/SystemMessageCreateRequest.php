<?php

namespace App\System\Request\Message;

use Hyperf\Validation\Request\FormRequest;

/**
 * 信息管理验证数据类 (Create)
 */
class SystemMessageCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required'],
        ];
    }

    public function attributes(): array
    {
        return [
            'content' => '内容',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '内容不能为空',
        ];
    }
}
