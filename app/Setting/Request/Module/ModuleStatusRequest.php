<?php


namespace App\Setting\Request\Module;


use Hyperf\Validation\Request\FormRequest;

class ModuleStatusRequest extends FormRequest
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
            'status' => 'required',
        ];
    }
}