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

use App\Model\Enums\User\Status;
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

    public function enablePageOrderBy(): bool
    {
        return false;
    }

    public function list(array $params = []): \Hyperf\Collection\Collection
    {
        return $this->perQuery($this->getQuery(), $params)->orderBy('sort')->get();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        $whereInName = static function (Builder $query, array|string $code) {
            $query->whereIn('name', Arr::wrap($code));
        };
        return $query
            ->when(Arr::get($params, 'sortable'), static function (Builder $query, array $sortable) {
                $query->orderBy(key($sortable), current($sortable));
            })
            ->when(Arr::get($params, 'code'), $whereInName)
            ->when(Arr::get($params, 'name'), $whereInName)
            ->when(Arr::get($params, 'children'), static function (Builder $query) {
                $query->with('children');
            })->when(Arr::get($params, 'status'), static function (Builder $query, Status $status) {
                $query->where('status', $status);
            })
            ->when(Arr::has($params, 'parent_id'), static function (Builder $query) use ($params) {
                $query->where('parent_id', Arr::get($params, 'parent_id'));
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
