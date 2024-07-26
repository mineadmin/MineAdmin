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

namespace App\Service\Settings;

use App\Repository\Settings\ConfigRepository;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\DbConnection\Annotation\Transactional;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Interfaces\ServiceInterface\ConfigServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

#[DependProxy(values: [ConfigServiceInterface::class])]
class ConfigService extends AbstractService implements ConfigServiceInterface
{
    /**
     * @var ConfigRepository
     */
    public $repository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        ConfigRepository $repository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->repository = $repository;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * 按key获取配置，并缓存.
     */
    #[Cacheable(
        prefix: 'system:config:value',
        value: '_#{key}',
        ttl: 600,
        listener: 'system-config-update'
    )]
    public function getConfigByKey(string $key): ?array
    {
        return $this->repository->getConfigByKey($key);
    }

    /**
     * 按组的key获取一组配置信息.
     */
    public function getConfigByGroupKey(string $groupKey): ?array
    {
        return $this->repository->getConfigByGroupKey($groupKey);
    }

    /**
     * 清除缓存.
     */
    #[CacheEvict(
        prefix: 'system:config:value',
        value: '_#{key}',
        all: true
    )
    ]
    public function clearCache(): bool
    {
        return true;
    }

    /**
     * 更新配置.
     */
    #[CacheEvict(
        prefix: 'system:config:value',
        value: '_#{key}'
    )]
    public function updated(string $key, array $data): bool
    {
        return $this->repository->updateConfig($key, $data);
    }

    /**
     * 按 keys 更新配置.
     */
    #[Transactional]
    public function updatedByKeys(array $data): bool
    {
        foreach ($data as $name => $value) {
            $this->dispatcher->dispatch(new DeleteListenerEvent(
                'system-config-update',
                [
                    (string) $name,
                ]
            ));
            $this->repository->updateByKey((string) $name, $value);
        }
        return true;
    }
}
