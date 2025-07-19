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

use App\Model\Permission\Role;
use App\Model\Permission\User;
use App\Repository\Permission\RoleRepository;
use App\Repository\Permission\UserRepository;
use App\Service\IService;
use Hyperf\Collection\Collection;
use Psr\SimpleCache\CacheInterface;

/**
 * @extends IService<User>
 */
final class UserService extends IService
{
    public function __construct(
        protected readonly UserRepository $repository,
        protected readonly RoleRepository $roleRepository,
        protected readonly CacheInterface $cache
    ) {}

    public function getInfo(int $id): ?User
    {
        if ($this->cache->has((string) $id)) {
            return $this->cache->get((string) $id);
        }
        $user = $this->repository->findById((string) $id);
        $this->cache->set((string) $id, $user, 60);
        return $user;
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
}
