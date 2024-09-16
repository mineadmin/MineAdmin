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

use App\Constants\User\Status;
use App\Model\Permission\Menu;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;

final class MenuRepository extends IRepository
{
    public function __construct(
        protected readonly Menu $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'sortable'), static function (Builder $query, array $sortable) {
                $query->orderBy(key($sortable), current($sortable));
            })
            ->when(Arr::get($params, 'code'), static function (Builder $query, array|string $code) {
                $query->whereIn('code', Arr::wrap($code));
            })
            ->when(Arr::has($params, 'children'), static function (Builder $query) {
                $query->with('children');
            })->when(Arr::get($params, 'status'), static function (Builder $query, Status $status) {
                $query->where('status', $status);
            })
            ->when(Arr::get($params, 'parent_id'), static function (Builder $query, int $parent_id) {
                $query->where('parent_id', $parent_id);
            });
    }

    public function allTree(): Collection
    {
        return $this->model
            ->newQuery()
            ->where('parent_id', 0)
            ->with('children')
            ->get();
    }
}
