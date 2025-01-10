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
use Hyperf\DbConnection\Model\Model;
use Hyperf\DbConnection\Traits\HasContainer;

/**
 * @template T of Model
 * @property IRepository<T> $repository
 */
abstract class IService
{
    use HasContainer;

    public function count(array $params): int
    {
        return $this->repository->count($params);
    }

    public function page(array $params, int $page = 1, int $pageSize = 10): array
    {
        return $this->repository->page($params, $page, $pageSize);
    }

    public function getList(array $paras): Collection
    {
        return $this->repository->list($paras);
    }

    /**
     * @return T
     */
    public function create(array $data): mixed
    {
        return $this->repository->create($data);
    }

    /**
     * @return T
     */
    public function save(array $data): mixed
    {
        return $this->create($data);
    }

    /**
     * @return null|mixed|T
     */
    public function updateById(mixed $id, array $data): mixed
    {
        return $this->repository->updateById($id, $data);
    }

    public function deleteById(mixed $id): int
    {
        return $this->repository->deleteById($id);
    }

    /**
     * @return null|T
     */
    public function findById(mixed $id): mixed
    {
        return $this->repository->findById($id);
    }

    public function existsById(mixed $id): bool
    {
        return $this->repository->existsById($id);
    }
}
