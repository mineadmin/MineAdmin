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

use App\Model\Permission\Position;
use App\Repository\IRepository;
use Hyperf\Database\Model\Builder;

/**
 * @extends IRepository<Position>
 */
final class PositionRepository extends IRepository
{
    public function __construct(
        protected readonly Position $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(isset($params['name']), static function (Builder $query) use ($params) {
                $query->where('name', 'like', '%' . $params['name'] . '%');
            })
            ->when(isset($params['dept_id']), static function (Builder $query) use ($params) {
                $query->where('dept_id', $params['dept_id']);
            })
            ->when(isset($params['created_at']), static function (Builder $query) use ($params) {
                $query->whereBetween('created_at', $params['created_at']);
            })
            ->when(isset($params['updated_at']), static function (Builder $query) use ($params) {
                $query->whereBetween('updated_at', $params['updated_at']);
            })
            ->with(['department', 'policy']);
    }
}
