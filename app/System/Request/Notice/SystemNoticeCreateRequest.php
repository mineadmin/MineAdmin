<?php

namespace App\System\Request\Notice;

use Hyperf\Validation\Request\FormRequest;

/**
 * 通知管理验证数据类 (Create)
 */
class SystemNoticeCreateRequest extends FormRequest
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
            
        ];
    }
}