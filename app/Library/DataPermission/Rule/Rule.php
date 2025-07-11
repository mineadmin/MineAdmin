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

namespace App\Library\DataPermission\Rule;

use App\Library\DataPermission\Rule\Executable\CreatedByIdsExecute;
use App\Library\DataPermission\Rule\Executable\DeptExecute;
use App\Library\DataPermission\ScopeType;
use App\Model\DataPermission\Policy;
use App\Model\Permission\User;
use Hyperf\Cache\CacheManager;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Query\Builder;
use Psr\SimpleCache\CacheInterface;

class Rule
{
    private readonly CacheInterface $cache;

    public function __construct(
        private readonly ConfigInterface $config,
        private readonly CacheManager $cacheManager
    ) {
        $this->cache = $this->cacheManager->getDriver($this->config->get('department.cache.driver'));
    }

    public function isCache(): bool
    {
        return (bool) $this->config->get('department.cache.enable');
    }

    public function getTtl(): mixed
    {
        return $this->config->get('department.cache.ttl');
    }

    public function getPrefix(): string
    {
        return $this->config->get('department.cache.prefix');
    }

    public function getDeptIds(User $user, Policy $policy): array
    {
        $cacheKey = $this->getPrefix() . ':deptIds:' . $user->id;
        if ($this->isCache() && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $execute = new DeptExecute($user, $policy);
        $result = $execute->execute();
        $this->cache->set($cacheKey, $result, $this->getTtl());
        return $result;
    }

    public function getCreatedByList(User $user, Policy $policy): array
    {
        $cacheKey = $this->getPrefix() . ':createdBy:' . $user->id;
        if ($this->isCache() && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $execute = new CreatedByIdsExecute($user, $policy);
        $result = $execute->execute();
        $this->cache->set($cacheKey, $result, $this->getTtl());
        return $result;
    }

    public function loadCustomFunc(string $customFunc, Builder $builder, User $user, ?Policy $policy, ScopeType $scopeType): void
    {
        $func = $this->config->get('department.custom.' . $customFunc);
        if ($func === null) {
            throw new \RuntimeException(\sprintf('Custom function %s not found', $customFunc));
        }
        $func($builder, $scopeType, $policy, $user);
    }
}
