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

class SystemApiRequest extends MineFormRequest
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
            'name' => 'required',
            'class_name' => 'required',
            'method_name' => 'required',
            'access_name' => 'required',
            'auth_mode' => 'required',
            'request_mode' => 'required',
            'group_id' => 'required',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            'name' => 'required',
            'class_name' => 'required',
            'method_name' => 'required',
            'auth_mode' => 'required',
            'request_mode' => 'required',
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
            'id' => '接口ID',
            'name' => '接口名称',
            'class_name' => '类命名空间地址',
            'method_name' => '方法名',
            'auth_mode' => '验证模式',
            'request_mode' => '请求方式',
            'status' => '接口状态',
        ];
    }
}
