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

namespace App\Repository\Permission;

use App\Model\Permission\Menu;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class MenuRepository extends IRepository
{
    public function __construct(
        protected readonly Menu $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query->when(Arr::get($params, 'user_id'), function (Builder $query, $userId) {
            $query->whereHas('roles', function (Builder $query) use ($userId) {
                $query->whereHas('users', function (Builder $query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            });
        })->when(Arr::get($params, 'sortable'), function (Builder $query, $sortable) {
            $query->orderBy(key($sortable), current($sortable));
        });
    }

    /**
     * 获取用户菜单.
     */
    public function getTreeMenuByUid(int $userId): array
    {
        return $this->getQuery(['user_id' => $userId, 'sortable' => ['sort' => 'asc']])
            ->with(['children', 'children.children', 'children.children.children'])
            ->get()
            ->toArray();
    }
}
