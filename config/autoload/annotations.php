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
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
            BASE_PATH . '/kernel',
        ],
        'collectors' => [],
        'ignore_annotations' => [],
    ],
];
