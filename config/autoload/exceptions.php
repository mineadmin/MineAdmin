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
    'handler' => [
        'http' => [
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            Mine\Exception\Handler\ValidationExceptionHandler::class,
            Mine\Exception\Handler\TokenExceptionHandler::class,
            Mine\Exception\Handler\NoPermissionExceptionHandler::class,
            Mine\Exception\Handler\NormalStatusExceptionHandler::class,
            Mine\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
