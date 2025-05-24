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
     * 每次组装数据权限时，都会重新查询数据库，这样可以保证数据权限的实时性，但是会增加数据库的压力
     * 如果你的数据权限不会频繁变动，可以开启缓存，缓存时间可以根据实际情况设置
     */
    'enable' => env('DATA_PERMISSION_CACHE_ENABLE', false),
    /*
     * 缓存时间，单位秒
     */
    'ttl' => env('DATA_PERMISSION_CACHE_TTL', 60 * 5),
    /*
     * 缓存前缀
     */
    'prefix' => env('DATA_PERMISSION_CACHE_PREFIX', 'data_permission'),
    /*
     * 缓存驱动
     */
    'driver' => env('DATA_PERMISSION_CACHE_DRIVER', 'default'),
];
