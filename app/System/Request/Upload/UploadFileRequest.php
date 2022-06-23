<?php

namespace App\System\Request\Upload;

use Hyperf\Validation\Request\FormRequest;

class UploadFileRequest extends FormRequest
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
            'file' => 'required|mimes:txt,doc,docx,xls,xlsx,ppt,pptx,rar,zip,7z,gz,pdf,wps,md',
            'path' => 'max:30',
            'isChunk' => '',
        ];
    }
}
{

}