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

class SystemMenuRequest extends MineFormRequest
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
            'name' => 'required|max:30',
            'code' => 'required|min:3|max:50',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            'name' => 'required|max:30',
            'code' => 'required|min:3|max:50',
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
            'id' => '菜单ID',
            'name' => '菜单名称',
            'code' => '菜单标识',
            'status' => '菜单状态',
        ];
    }
}
