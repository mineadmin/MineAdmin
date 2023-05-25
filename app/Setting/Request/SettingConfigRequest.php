<?php

declare(strict_types=1);
namespace App\Setting\Request;

use Mine\MineFormRequest;

class SettingConfigRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [
            'group_id' => 'required',
            'key' => 'required|max:32',
            'name' => 'required|max:255',
            'input_type' => '',
            'config_select_data' => '',
            'sort' => '',
            'remark' => '',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function saveRules(): array
    {
        return [
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function updateRules(): array
    {
        return [
        ];
    }

    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'group_id' => '组ID',
            'key' => '配置键名',
            'name' => '配置名称',
        ];
    }
}