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
use App\Repository\Permission\RoleRepository;
use App\Service\IService;

/**
 * @extends IService<Role>
 */
class RoleService extends IService
{
    public function __construct(
        protected readonly RoleRepository $repository
    ) {}

    public function batchGrantPermissionsForRole(int $id, array $menuIds): void
    {
        $entity = $this->repository->findById($id);
        // @phpstan-ignore-next-line
        $entity->menus()->sync($menuIds);
    }
}
