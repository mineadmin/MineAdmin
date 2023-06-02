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

return [
    // 是否启用数据权限
    'data_scope_enabled' => true,
    /**
     * excel 导入、导出驱动类型 auto, xlsWriter, phpOffice
     * auto 优先使用xlsWriter，若环境没有安装xlsWriter扩展则使用phpOffice
     */
    'excel_drive' => 'auto',
    // 是否启用 远程通用列表查询 功能
    'remote_api_enabled' => true,
];