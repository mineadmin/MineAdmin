<?php

namespace App\System\Request\Upload;

use Hyperf\Validation\Request\FormRequest;

class NetworkImageRequest extends FormRequest
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
            'url'   => 'required',
            'path'  => 'max:30',
        ];
    }
}
{

}