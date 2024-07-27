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

use App\Http\Common\Middleware\CorsMiddleware;
use App\Http\Common\Middleware\RequestIdMiddleware;
use App\Http\Common\Middleware\TranslationMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        // 跨域中间件，正式环境建议关闭。使用 Nginx 等代理服务器处理跨域问题。
        CorsMiddleware::class,
        // 验证器中间件,处理 formRequest 验证器
        ValidationMiddleware::class,
        // 请求ID中间件
        RequestIdMiddleware::class,
        // 多语言识别中间件
        TranslationMiddleware::class
    ],
];
