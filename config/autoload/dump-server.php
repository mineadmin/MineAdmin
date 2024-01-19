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
     * The host to use when listening for debug server connections.
     */
    'host' => env('DUMP_SERVER_HOST', 'tcp://127.0.0.1:9912'),
];
