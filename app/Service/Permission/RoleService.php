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
use App\Model\Permission\Role;
use App\Repository\Permission\MenuRepository;
use App\Repository\Permission\RoleRepository;
use App\Service\IService;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Db;

/**
 * @extends IService<Role>
 */
final class RoleService extends IService
{
    public function __construct(
        protected readonly RoleRepository $repository,
        protected readonly MenuRepository $menuRepository
    ) {}

    public function getRolePermission(int $id): Collection
    {
        // @phpstan-ignore-next-line
        return $this->repository->findById($id)->menus()->get();
    }

    public function batchGrantPermissionsForRole(int $id, array $permissionsCode): void
    {
        if (\count($permissionsCode) === 0) {
            // @phpstan-ignore-next-line
            $this->repository->findById($id)->menus()->detach();
            return;
        }
        // @phpstan-ignore-next-line
        $this->repository->findById($id)
            ->menus()
            ->sync(
                $this->menuRepository
                    ->list([
                        'code' => $permissionsCode,
                    ])
                    ->map(static fn ($item) => $item->id)
                    ->toArray()
            );
    }

    public function create(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            /**
             * @var Role $entity
             */
            $entity = parent::create($data);
            if (isset($data['policy'])) {
                $entity->policy()->create($data['policy']);
            }
        });
    }

    public function updateById(mixed $id, array $data): mixed
    {
        return Db::transaction(function () use ($id, $data) {
            /**
             * @var null|Role $entity
             */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(code: ResultCode::NOT_FOUND);
            }
            $entity->update($data);
            if (isset($data['policy'])) {
                $entity->policy()->update($data['policy']);
            }
        });
    }
}
