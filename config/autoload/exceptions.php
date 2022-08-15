<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
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
