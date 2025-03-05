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
use App\Model\Permission\User;
use App\Repository\Permission\RoleRepository;
use App\Repository\Permission\UserRepository;
use App\Service\IService;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Db;

/**
 * @extends IService<User>
 */
final class UserService extends IService
{
    public function __construct(
        protected readonly UserRepository $repository,
        protected readonly RoleRepository $roleRepository
    ) {}

    public function getInfo(int $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function resetPassword(?int $id): bool
    {
        if ($id === null) {
            return false;
        }
        $entity = $this->repository->findById($id);
        $entity->resetPassword();
        $entity->save();
        return true;
    }

    public function getUserRole(int $id): Collection
    {
        return $this->repository->findById($id)->roles()->get();
    }

    public function batchGrantRoleForUser(int $id, array $roleCodes): void
    {
        $this->repository->findById($id)
            ->roles()
            ->sync(
                $this->roleRepository->list([
                    'code' => $roleCodes,
                ])->map(static function (Role $role) {
                    return $role->id;
                })
            );
    }

    public function create(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            /** @var User $entity */
            $entity = parent::create($data);
            if (! empty($data['policy'])) {
                $entity->policy()->create($data['policy']);
            }
        });
    }

    public function updateById(mixed $id, array $data): mixed
    {
        return Db::transaction(function () use ($id, $data) {
            /** @var User $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->fill($data)->save();
            if (! empty($data['policy'])) {
                $entity->policy()->update($data['policy']);
            }
        });
    }
}
