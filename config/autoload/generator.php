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
    'dto' => [
        'namespace' => 'App\\Dto',
        'path' => BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Dto',
        'type' => [
            'mapping' => [
                'integer' => 'int',
            ],
            'format' => [
                'datetime' => [
                    'attribute' => [
                    ],
                ],
            ],
        ],
    ],
    'mapper' => [
        'namespace' => 'App\\Mapper',
        'path' => BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mapper',
    ],
    'service' => [
        'namespace' => 'App\\Service',
        'path' => BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Service',
        'impl' => 'Impl',
    ],
];
