<?php

namespace App\System\Request\Upload;

use Hyperf\Validation\Request\FormRequest;

class UploadImageRequest extends FormRequest
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
            'image' => 'required|image',
            'path'  => 'max:30',
        ];
    }
}
{

}