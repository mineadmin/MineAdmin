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

namespace App\Http\Admin\Request;

use Mine\MineFormRequest;

class CrontabRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'rule' => 'required',
            'target' => 'required',
        ];
    }

    /**
     * 新增数据验证规则.
     */
    public function saveRules(): array
    {
        return [];
    }

    /**
     * 新增数据验证规则.
     */
    public function updateRules(): array
    {
        return [];
    }

    /**
     * 字段映射名称.
     */
    public function attributes(): array
    {
        return [
            'name' => '任务名称',
            'type' => '任务类型',
            'target' => '调用目标',
            'rule' => '定时规则表达式',
        ];
    }
}
