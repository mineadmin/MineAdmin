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
return [
    /*
     * These plugins will be automatically installed during system installation.
     * Only plugins in this list can be automatically installed via the system.
     *
     * 在系统安装时会自动安装这些插件。
     * 只有在此列表中的插件才能通过系统自动安装。
     */
    'allow_auto_install_plugins' => [
        'mine-admin/code-generator',    // 代码生成器
    ],
];
