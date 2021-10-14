<?php


namespace App\Setting\Request\Module;


use Hyperf\Validation\Request\FormRequest;

class ModuleCreateRequest extends FormRequest
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
            'name' => 'required|regex:/^[A-Za-z]{2,}$/i',
            'label' => 'required',
            'version' => 'required|regex:/^[0-9\.]{3,}$/',
            'description' => 'required|max:255',
        ];
    }
}