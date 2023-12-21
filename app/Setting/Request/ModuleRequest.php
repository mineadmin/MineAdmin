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

class ModuleRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            'name' => 'required|regex:/^[A-Za-z]{2,}$/i',
            'label' => 'required',
            'version' => 'required|regex:/^[0-9\.]{3,}$/',
            'description' => 'required|max:255',
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function modifyStatusRules(): array
    {
        return [
            'name' => 'required',
            'status' => 'required',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'name' => '模块名称',
            'label' => '模块标识',
            'version' => '模块版本号',
            'description' => '模块描述',
            'status' => '模块状态',
        ];
    }
}
