<?php


namespace App\Setting\Request\Tool;


use Hyperf\Validation\Request\FormRequest;

class GenerateUpdateRequest extends FormRequest
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
            'id' => 'required',
            'generate_type' => 'required',
            'build_menu' => 'required',
            'generate_menus' => 'required|array',
            'menu_name' => 'required',
            'module_name' => 'required',
            'package_name' => '',
            'table_comment' => 'required',
            'table_name' => 'required',
            'type' => 'required',
            'columns' => 'required|array',
            'belong_menu_id' => '',
            'options' => '',
            'remark' => '',
        ];
    }
}