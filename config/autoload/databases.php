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
use Hyperf\Database\Commands\ModelOption;
return [
    'default' => [
        'driver' => \Hyperf\Support\env('DB_DRIVER', 'mysql'),
        'host' => \Hyperf\Support\env('DB_HOST', 'localhost'),
        'database' => \Hyperf\Support\env('DB_DATABASE', 'hyperf'),
        'port' => \Hyperf\Support\env('DB_PORT', 3306),
        'username' => \Hyperf\Support\env('DB_USERNAME', 'root'),
        'password' => \Hyperf\Support\env('DB_PASSWORD', 'root'),
        'charset' => \Hyperf\Support\env('DB_CHARSET', 'utf8mb4'),
        'collation' => \Hyperf\Support\env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix' => \Hyperf\Support\env('DB_PREFIX', ''),
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 20,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) \Hyperf\Support\env('DB_MAX_IDLE_TIME', 60),
        ],
        'cache' => [
            'handler' => \Hyperf\ModelCache\Handler\RedisHandler::class,
            'cache_key' => 'MineAdmin:%s:m:%s:%s:%s',
            'prefix' => 'model-cache',
            'ttl' => 86400 * 7,
            'empty_model_ttl' => 60,
            'load_script' => true,
            'use_default_value' => false,
        ],
        'commands' => [
            'gen:model' => [
                'path' => 'app/Model',
                'force_casts' => true,
                'inheritance' => 'MineModel',
                'uses' => 'Mine\MineModel',
                'with_comments' => true,
                'refresh_fillable' => true,
                'visitors' => [
                    Hyperf\Database\Commands\Ast\ModelRewriteKeyInfoVisitor::class,
                    Hyperf\Database\Commands\Ast\ModelRewriteTimestampsVisitor::class,
                    Hyperf\Database\Commands\Ast\ModelRewriteSoftDeletesVisitor::class,
//                    Hyperf\Database\Commands\Ast\ModelRewriteGetterSetterVisitor::class,
                ],
            ],
        ],
    ],
];
