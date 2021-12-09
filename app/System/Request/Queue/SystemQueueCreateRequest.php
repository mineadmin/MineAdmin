<?php

namespace App\System\Request\Queue;

use Hyperf\Validation\Request\FormRequest;

/**
 * 队列管理验证数据类 (Create)
 */
class SystemQueueCreateRequest extends FormRequest
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