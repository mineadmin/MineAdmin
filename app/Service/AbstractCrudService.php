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

namespace App\Service;

use App\Repository\IRepository;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Traits\HasContainer;

/**
 * @template T
 * @property-read IRepository<T>|class-string<IRepository<T>> $repository
 */
class AbstractCrudService
{
    use HasContainer;

    public function page(array $params, int $page = 1, int $pageSize = 10): array
    {
        return $this->getRepository()->page($params, $page, $pageSize);
    }

    public function getList(array $paras): Collection
    {
        return $this->getRepository()->list($paras);
    }

    /**
     * @return T
     */
    public function create(array $data): mixed
    {
        return $this->getRepository()->create($data);
    }

    /**
     * @return T
     */
    public function save(array $data): mixed
    {
        return $this->create($data);
    }

    /**
     * @return null|T
     */
    public function updateById(mixed $id, array $data): mixed
    {
        return $this->getRepository()->updateById($id, $data);
    }

    public function deleteById(mixed $id, bool $force = true): bool
    {
        return $force ? $this->getRepository()->forceDeleteById($id) : $this->getRepository()->deleteById($id);
    }

    /**
     * @return null|T
     */
    public function findById(mixed $id): mixed
    {
        return $this->getRepository()->findById($id);
    }

    /**
     * @return IRepository<T>
     */
    public function getRepository(): mixed
    {
        if (! empty($this->repository) && is_object($this->repository)) {
            return $this->repository;
        }
        if (class_exists($this->repository) || interface_exists($this->repository)) {
            return $this->getContainer()->get($this->repository);
        }

        throw new \RuntimeException(sprintf('Cannot detect the repository of %s', static::class));
    }
}
