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

namespace App\Setting\Service;

use App\Setting\Mapper\SettingConfigMapper;
use Hyperf\Config\Annotation\Value;
use Hyperf\Redis\Redis;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Annotation\Transaction;
use Mine\Interfaces\ServiceInterface\ConfigServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[DependProxy(values: [ConfigServiceInterface::class])]
class SettingConfigService extends AbstractService implements ConfigServiceInterface
{
    /**
     * @var SettingConfigMapper
     */
    public $mapper;

    protected ContainerInterface $container;

    protected Redis $redis;

    #[Value('cache.default.prefix')]
    protected string $prefix;

    protected string $cacheGroupName;

    protected string $cacheName;

    /**
     * SettingConfigService constructor.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(SettingConfigMapper $mapper, ContainerInterface $container)
    {
        $this->mapper = $mapper;
        $this->container = $container;
        $this->setCacheGroupName($this->prefix . 'configGroup:');
        $this->setCacheName($this->prefix . 'config:');
        $this->redis = $this->container->get(Redis::class);
    }

    /**
     * 按key获取配置，并缓存.
     * @throws \RedisException
     */
    public function getConfigByKey(string $key): ?array
    {
        if (empty($key)) {
            return [];
        }
        $cacheKey = $this->getCacheName() . $key;
        if ($data = $this->redis->get($cacheKey)) {
            return unserialize($data);
        }
        $data = $this->mapper->getConfigByKey($key);
        if ($data) {
            $this->redis->set($cacheKey, serialize($data));
            return $data;
        }
        return null;
    }

    /**
     * 按组的key获取一组配置信息.
     */
    public function getConfigByGroupKey(string $groupKey): ?array
    {
        return $this->mapper->getConfigByGroupKey($groupKey);
    }

    /**
     * 清除缓存.
     * @throws \RedisException
     */
    public function clearCache(): bool
    {
        $groupCache = $this->redis->keys($this->getCacheGroupName() . '*');
        $keyCache = $this->redis->keys($this->getCacheName() . '*');
        foreach ($groupCache as $item) {
            $this->redis->del($item);
        }

        foreach ($keyCache as $item) {
            $this->redis->del($item);
        }

        return true;
    }

    /**
     * 更新配置.
     */
    public function updated(string $key, array $data): bool
    {
        return $this->mapper->updateConfig($key, $data);
    }

    /**
     * 按 keys 更新配置.
     */
    #[Transaction]
    public function updatedByKeys(array $data): bool
    {
        foreach ($data as $name => $value) {
            $this->mapper->updateByKey((string) $name, $value);
        }
        return true;
    }

    public function getCacheGroupName(): string
    {
        return $this->cacheGroupName;
    }

    public function setCacheGroupName(string $cacheGroupName): void
    {
        $this->cacheGroupName = $cacheGroupName;
    }

    public function getCacheName(): string
    {
        return $this->cacheName;
    }

    public function setCacheName(string $cacheName): void
    {
        $this->cacheName = $cacheName;
    }
}
