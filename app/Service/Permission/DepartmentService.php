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

namespace App\Service\Permission;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Model\Permission\Department;
use App\Repository\Permission\DepartmentRepository;
use App\Service\IService;
use Hyperf\DbConnection\Db;

/**
 * @extends IService<Department>
 */
class DepartmentService extends IService
{
    public function __construct(
        protected readonly DepartmentRepository $repository
    ) {}

    public function create(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            $entity = $this->repository->create($data);
            $this->handleEntity($entity, $data);
            return $entity;
        });
    }

    public function updateById(mixed $id, array $data): mixed
    {
        return Db::transaction(function () use ($id, $data) {
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $this->handleEntity($entity, $data);
        });
    }

    public function getPositionsByDepartmentId(int $id): array
    {
        $entity = $this->repository->findById($id);
        if (empty($entity)) {
            throw new BusinessException(ResultCode::NOT_FOUND);
        }
        return $entity->positions()->get(['id', 'name'])->toArray();
    }

    protected function handleEntity(Department $entity, array $data): void
    {
        if (isset($data['department_users'])) {
            $entity->department_users()->sync($data['department_users']);
        }
        if (isset($data['leader'])) {
            $entity->leader()->sync($data['leader']);
        }
    }
}
