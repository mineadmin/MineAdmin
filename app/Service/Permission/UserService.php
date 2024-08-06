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

use App\Model\Permission\User;
use App\Repository\Permission\MenuRepository;
use App\Repository\Permission\RoleRepository;
use App\Repository\Permission\UserRepository;
use App\Service\IService;
use Casbin\Enforcer;
use Hyperf\Collection\Collection;

/**
 * @extends IService<UserRepository>
 */
final class UserService extends IService
{
    public function __construct(
        protected readonly UserRepository $repository,
        private readonly Enforcer $enforcer,
        private readonly MenuRepository $menuRepository,
        private readonly RoleRepository $roleRepository
    ) {}

    public function getInfo(int $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function getFieldByUserId(int $userId, string $field): mixed
    {
        return $this->repository->getQuery([
            'id' => $userId,
        ])->value($field);
    }

    public function getMenuTreeByUserId(int $userId): Collection
    {
        $user = $this->getInfo($userId);
        $permissions = $this->getEnforce()->getPermissionsForUser($user->username);
        return $this->menuRepository->getMenuByCode($permissions);
    }

    public function getRolesByUserId(int $userId): Collection
    {
        $user = $this->getInfo($userId);
        $roleCodes = $this->getEnforce()->getRolesForUser($user->username);
        return $this->roleRepository->getQuery([
            'code' => $roleCodes,
        ])->get();
    }

    private function getEnforce(): Enforcer
    {
        return $this->enforcer;
    }
}
