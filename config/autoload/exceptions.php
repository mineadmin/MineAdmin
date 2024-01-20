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
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Mine\Exception\Handler\AppExceptionHandler;
use Mine\Exception\Handler\NoPermissionExceptionHandler;
use Mine\Exception\Handler\NormalStatusExceptionHandler;
use Mine\Exception\Handler\TokenExceptionHandler;
use Mine\Exception\Handler\ValidationExceptionHandler;

return [
    'handler' => [
        'http' => [
            HttpExceptionHandler::class,
            ValidationExceptionHandler::class,
            TokenExceptionHandler::class,
            NoPermissionExceptionHandler::class,
            NormalStatusExceptionHandler::class,
            AppExceptionHandler::class,
        ],
    ],
];
