<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Setting\Request;

use Mine\MineFormRequest;

class SettingConfigGroupRequest extends MineFormRequest
{
    /**
     * 公共规则.
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
            'id' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
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
