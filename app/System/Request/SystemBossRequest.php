<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */
namespace App\System\Request;

use Mine\MineFormRequest;

/**
 * 老板管理验证数据类
 */
class SystemBossRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    
    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //老板名称 验证
            'name' => 'required',
            //老板代码 验证
            'code' => 'required',
            //排序 验证
            'sort' => 'required',
            //状态 (0正常 1停用) 验证
            'status' => 'required',

        ];
    }
    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //老板名称 验证
            'name' => 'required',
            //老板代码 验证
            'code' => 'required',
            //排序 验证
            'sort' => 'required',
            //状态 (0正常 1停用) 验证
            'status' => 'required',

        ];
    }

    
    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'name' => '老板名称',
            'code' => '老板代码',
            'sort' => '排序',
            'status' => '状态 (0正常 1停用)',

        ];
    }

}