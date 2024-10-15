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
use Hyperf\Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;

/**
 * @extends IService<Role>
 */
final class RoleService extends IService
{
    public function __construct(
        protected readonly RoleRepository $repository
    ) {}

    public function getRolePermission(int $id): Collection
    {
        // @phpstan-ignore-next-line
        return $this->repository->findById($id)->menus()->get();
    }

    public function batchGrantPermissionsForRole(int $id, array $menuIds): void
    {
        $entity = $this->repository->findById($id);
        $syncData = [];
        $currentTime = Carbon::now();
        Arr::map($menuIds, static function ($menuId) use (&$syncData, $currentTime) {
            $syncData[$menuId] = [
                'ptype' => 'p',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        });
        // @phpstan-ignore-next-line
        $entity->menus()->sync($syncData);
    }
}
