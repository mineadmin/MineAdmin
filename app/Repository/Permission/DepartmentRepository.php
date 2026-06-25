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

use App\Model\Permission\Department;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;

/**
 * @extends IRepository<Department>
 */
final class DepartmentRepository extends IRepository
{
    public function __construct(
        protected readonly Department $model
    ) {}

    public function getByIds(array $ids): Collection
    {
        return $this->model->newQuery()->whereIn('id', $ids)->get();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(isset($params['id']), static function (Builder $query) use ($params) {
                $query->whereIn('id', Arr::wrap($params['id']));
            })
            ->when(isset($params['name']), static function (Builder $query) use ($params) {
                $query->where('name', 'like', '%' . $params['name'] . '%');
            })
            ->when(isset($params['parent_id']), static function (Builder $query) use ($params) {
                $query->where('parent_id', $params['parent_id']);
            })
            ->when(isset($params['created_at']), static function (Builder $query) use ($params) {
                $query->whereBetween('created_at', $params['created_at']);
            })
            ->when(isset($params['updated_at']), static function (Builder $query) use ($params) {
                $query->whereBetween('updated_at', $params['updated_at']);
            })
            ->when(! empty($params['append_position']), static function (Builder $query) {
                $query->with('positions:id,name');
            })
            ->when(isset($params['level']), static function (Builder $query) use ($params) {
                if ((int) $params['level'] === 1) {
                    $query->where('parent_id', 0);
                    $query->with('children');
                }

                // todo 指定层级查询
            })
            ->with(['positions', 'department_users:id,nickname,username,avatar,phone,email', 'leader:id,nickname,username,avatar']);
    }
}
