<?php

declare(strict_types=1);
namespace App\Setting\Service;


use App\Setting\Mapper\SettingConfigMapper;
use Hyperf\Config\Annotation\Value;
use Hyperf\Redis\Redis;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Psr\Container\ContainerInterface;

class SettingConfigService extends AbstractService
{
    /**
     * @var SettingConfigMapper
     */
    public $mapper;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var Redis
     */
    protected Redis $redis;

    /**
     * @var string
     */
    #[Value("cache.default.prefix")]
    protected string $prefix;

    /**
     * @var string
     */
    protected string $cacheGroupName;

    /**
     * @var string
     */
    protected string $cacheName;

    /**
     * SettingConfigService constructor.
     * @param SettingConfigMapper $mapper
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * 按key获取配置，并缓存
     * @param string $key
     * @return array|null
     * @throws \RedisException
     */
    public function getConfigByKey(string $key): ?array
    {
        if (empty($key)) return [];
        $cacheKey = $this->getCacheName() . $key;
        if (($data = $this->redis->get($cacheKey))) {
            return unserialize($data);
        } else {
            $data = $this->mapper->getConfigByKey($key);
            if ($data) {
                $this->redis->set($cacheKey, serialize($data));
                return $data;
            }
            return null;
        }
    }

    /**
     * 清除缓存
     * @return bool
     * @throws \RedisException
     */
    public function clearCache(): bool
    {
        $groupCache = $this->redis->keys($this->getCacheGroupName().'*');
        $keyCache = $this->redis->keys($this->getCacheName().'*');
        foreach ($groupCache as $item) {
            $this->redis->del($item);
        }

        foreach($keyCache as $item) {
            $this->redis->del($item);
        }

        return true;
    }

    /**
     * 更新配置
     * @param string $key
     * @param array $data
     * @return bool
     */
    public function updated(string $key, array $data): bool
    {
        return $this->mapper->updateConfig($key, $data);
    }

    /**
     * 按 keys 更新配置
     * @param array $data
     * @return bool
     */
    #[Transaction]
    public function updatedByKeys(array $data): bool
    {
        foreach ($data as $name => $value) {
            $this->mapper->updateByKey((string) $name, $value);
        }
        return true;
    }

    /**
     * @return string
     */
    public function getCacheGroupName(): string
    {
        return $this->cacheGroupName;
    }

    /**
     * @param string $cacheGroupName
     */
    public function setCacheGroupName(string $cacheGroupName): void
    {
        $this->cacheGroupName = $cacheGroupName;
    }

    /**
     * @return string
     */
    public function getCacheName(): string
    {
        return $this->cacheName;
    }

    /**
     * @param string $cacheName
     */
    public function setCacheName(string $cacheName): void
    {
        $this->cacheName = $cacheName;
    }


}
