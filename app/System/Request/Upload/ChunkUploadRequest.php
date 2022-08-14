<?php

namespace App\System\Request\Upload;

use Hyperf\Validation\Request\FormRequest;

class ChunkUploadRequest extends FormRequest
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
            'package' => 'required',
            'total'   => 'required',
            'index'   => 'required',
            'hash'    => 'required',
            'ext'     => 'required',
            'type'    => 'required',
            'name'    => 'required',
            'size'    => 'required',
        ];
    }
}
{

}