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

namespace App\System\Request;

use Mine\MineFormRequest;

class SystemAppRequest extends MineFormRequest
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
            'group_id' => 'required',
            'app_name' => 'required',
            'app_id' => 'required',
            'app_secret' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            'group_id' => 'required',
            'app_name' => 'required',
            'app_id' => 'required',
            'app_secret' => 'required',
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => 'required',
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
            'id' => '应用ID',
            'group_id' => '应用分组',
            'app_name' => '应用名称',
            'app_id' => 'APP ID',
            'app_secret' => 'APP SECRET',
            'status' => '应用状态',
        ];
    }
}
