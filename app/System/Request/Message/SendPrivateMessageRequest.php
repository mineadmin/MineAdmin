<?php

namespace App\System\Request\Message;

use Hyperf\Validation\Request\FormRequest;

/**
 * 发私信验证
 */
class SendPrivateMessageRequest extends FormRequest
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
            'title' => 'required',
            'users' => 'required|array',
            'content' => 'required'
        ];
    }
}