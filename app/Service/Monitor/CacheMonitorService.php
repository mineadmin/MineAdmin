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

namespace App\Service\Monitor;

use Hyperf\Redis\Redis;

class CacheMonitorService
{
    public function getCacheServerInfo(): array
    {
        $redis = container()->get(Redis::class);

        $info = $redis->info();

        $iterator = null;
        $keys = [];
        while (false !== ($key = $redis->scan($iterator, config('cache.default.prefix') . '*', 100))) {
            $keys = array_merge($keys, $key);
        }

        return [
            'keys' => &$keys,
            'server' => [
                'version' => &$info['redis_version'],
                'redis_mode' => ($info['redis_mode'] === 'standalone') ? '单机' : '集群',
                'run_days' => &$info['uptime_in_days'],
                'aof_enabled' => ($info['aof_enabled'] == 0) ? '关闭' : '开启',
                'use_memory' => &$info['used_memory_human'],
                'port' => &$info['tcp_port'],
                'clients' => &$info['connected_clients'],
                'expired_keys' => &$info['expired_keys'],
                'sys_total_keys' => count($keys),
            ],
        ];
    }

    /**
     * 查看缓存内容.
     */
    public function view(string $key): string
    {
        return container()->get(Redis::class)->get($key) ?? '';
    }

    /**
     * 删除一个缓存.
     */
    public function delete(string $key): bool
    {
        return container()->get(Redis::class)->del($key) > 0;
    }

    /**
     * 清空所有缓存.
     */
    public function clear(): bool
    {
        return container()->get(Redis::class)->flushDB();
    }
}
