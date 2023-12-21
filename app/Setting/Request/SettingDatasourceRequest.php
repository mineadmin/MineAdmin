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

/**
 * 数据源管理验证数据类.
 */
class SettingDatasourceRequest extends MineFormRequest
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
            // 数据源名称 验证
            'source_name' => 'required',
            'dsn' => 'required',
            'username' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            // 数据源名称 验证
            'source_name' => 'required',
            'dsn' => 'required',
            'username' => 'required',
            'password' => 'required',
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
            'source_name' => '数据源名称',
            'dsn' => 'DSN连接字符串',
            'username' => '数据库用户',
            'password' => '数据库密码',
        ];
    }
}
