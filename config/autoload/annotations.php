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
use Mine\Annotation\Api\MApiRequestParamCollector;
use Mine\Annotation\Api\MApiResponseParamCollector;
use Mine\Annotation\DependProxyCollector;

return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
            BASE_PATH . '/api',
        ],
        // 初始化注解收集器
        'collectors' => [
            MApiRequestParamCollector::class,
            MApiResponseParamCollector::class,
            DependProxyCollector::class,
        ],
        'ignore_annotations' => [
            'mixin',
            'required',
        ],
    ],
];
