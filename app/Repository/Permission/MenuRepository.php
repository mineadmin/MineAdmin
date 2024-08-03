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
use Hyperf\Collection\Collection as BaseCollection;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;

use function App\Http\Admin\Support\data_to_tree;

final class MenuRepository extends IRepository
{
    public function __construct(
        protected readonly Menu $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'sortable'), function (Builder $query, array $sortable) {
                $query->orderBy(key($sortable), current($sortable));
            })
            ->when(Arr::get($params, 'code'), function (Builder $query, array|string $code) {
                is_array($code) ? $query->whereIn('code', $code) : $query->where('code', $code);
            });
    }

    /**
     * 获取用户菜单.
     */
    public function getTreeMenuByUid(int $userId): array
    {
        return $this->getQuery(['user_id' => $userId, 'sortable' => ['sort' => 'asc']])
            ->with(['children'])
            ->get()
            ->toArray();
    }

    /**
     * @return Collection<int,Menu>
     */
    public function getMenuByCode(array $code): Collection
    {
        return $this->getQuery(['code' => $code])
            ->get();
    }

    public function getMenuTreeByCode(array $code): BaseCollection
    {
        return data_to_tree($this->getMenuByCode($code));
    }
}