<?php


namespace App\Setting\Request\Setting;


use Mine\MineFormRequest;

class SettingConfigGroupRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [
            'name' => 'required|max:32',
            'code' => 'required|max:64',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function saveRules(): array
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function updateRules(): array
    {
        return [
            'id' => 'required'
        ];
    }

    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '主键',
            'name' => '配置组名称',
            'code' => '配置组标识',
        ];
    }
}