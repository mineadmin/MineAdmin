<?php

namespace App\Http\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file',
        ];
    }
}
