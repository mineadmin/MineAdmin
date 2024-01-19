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

use App\Setting\Mapper\SettingCrontabMapper;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DeleteCache;
use Mine\Crontab\MineCrontab;
use Mine\Crontab\MineExecutor;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class SettingCrontabService extends AbstractService
{
    /**
     * @var SettingCrontabMapper
     */
    public $mapper;

    #[Inject]
    protected ContainerInterface $container;

    protected Redis $redis;

    #[Value('cache.default.prefix')]
    protected string $prefix;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(SettingCrontabMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->redis = $this->container->get(Redis::class);
    }

    /**
     * 保存.
     * @throws \RedisException
     */
    public function save(array $data): int
    {
        $id = parent::save($data);
        $this->redis->del($this->prefix . 'crontab');

        return $id;
    }

    /**
     * 更新.
     * @throws \RedisException
     */
    public function update(mixed $id, array $data): bool
    {
        $res = parent::update($id, $data);
        $this->redis->del($this->prefix . 'crontab');

        return $res;
    }

    /*
    *
     * 删除
     * @param array $ids
     * @return bool
     * @throws \RedisException
     */
    public function delete(array $ids): bool
    {
        $res = parent::delete($ids);
        $this->redis->del($this->prefix . 'crontab');

        return $res;
    }

    /**
     * 立即执行一次定时任务
     * @param mixed $id
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run($id): ?bool
    {
        $crontab = new MineCrontab();
        $model = $this->read($id);
        $crontab->setCallback($model->target);
        $crontab->setType((string) $model->type);
        $crontab->setEnable(true);
        $crontab->setCrontabId($model->id);
        $crontab->setName($model->name);
        $crontab->setParameter($model->parameter ?: '');
        $crontab->setRule($model->rule);

        $executor = $this->container->get(MineExecutor::class);

        return $executor->execute($crontab, true);
    }

    #[DeleteCache('crontab')]
    public function changeStatus(mixed $id, string $value, string $filed = 'status'): bool
    {
        return parent::changeStatus($id, $value, $filed);
    }
}
