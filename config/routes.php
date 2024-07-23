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
use App\Ws\Admin\Middleware\WsAuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::get('/', function () {
    return 'welcome use mineAdmin';
});

Router::get('/favicon.ico', function () {
    return '';
});

// 消息ws服务器
Router::addServer('message', function () {
    Router::get('/message.io', 'App\Http\Admin\System\Controller\ServerController', [
        'middleware' => [WsAuthMiddleware::class],
    ]);
});
