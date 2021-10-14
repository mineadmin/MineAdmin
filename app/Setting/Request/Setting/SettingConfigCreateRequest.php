<?php


namespace App\Setting\Request\Setting;


use Hyperf\Validation\Request\FormRequest;

class SettingConfigCreateRequest extends FormRequest
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
            'key' => 'required|max:255',
            'value' => 'required|max:255',
            'name' => 'required|max:255',
            'group_name' => 'required|max:100',
            'remark' => '',
        ];
    }
}