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

use App\Model\Permission\Role;
use App\Repository\Permission\MenuRepository;
use App\Repository\Permission\RoleRepository;
use App\Service\Permission\UserService;
use Casbin\Enforcer;
use Hyperf\Collection\Collection;

final class PermissionService
{
    public function __construct(
        protected readonly UserService $userService,
        private readonly Enforcer $enforcer,
        private readonly MenuRepository $menuRepository,
        private readonly RoleRepository $roleRepository,
    ) {}

    public function getMenuTreeByUserId(int $userId): Collection
    {
        // 用户本身的菜单 codes
        $userMenuCodes = $this->getEnforce()->getImplicitPermissionsForUser(
            $this->userService->getFieldByUserId($userId, 'username')
        );
        $all = [];
        array_walk_recursive($userMenuCodes, function ($item) use (&$all) {
            $all[] = $item;
        });
        $all = array_unique($all);
        if (! $all) {
            return Collection::make();
        }
        return $this->menuRepository->getMenuTreeByCode(
            $all
        );
    }

    /**
     * @return Collection|Collection<int, Role>
     */
    public function getRolesByUserId(int $userId): Collection
    {
        $roleCodes = $this->getEnforce()->getImplicitRolesForUser(
            $this->userService->getFieldByUserId($userId, 'username')
        );
        $all = [];
        array_walk_recursive($roleCodes, function ($item) use (&$all) {
            $all[] = $item;
        });
        $all = array_unique($all);

        if (! $all) {
            return Collection::make();
        }
        return $this->roleRepository->getQuery([
            'code' => $all,
        ])->get();
    }

    public function getEnforce(): Enforcer
    {
        return $this->enforcer;
    }
}
