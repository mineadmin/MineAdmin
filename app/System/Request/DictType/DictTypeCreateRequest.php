<?php

namespace App\System\Request\DictType;

use Hyperf\Validation\Request\FormRequest;

class DictTypeCreateRequest extends FormRequest
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
            'name' => 'required',
            'code' => 'required',
        ];
    }
}
{

}