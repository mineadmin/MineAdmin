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

use App\Model\Permission\Leader;
use App\Repository\IRepository;
use Hyperf\Database\Model\Builder;

/**
 * @extends IRepository<Leader>
 */
final class LeaderRepository extends IRepository
{
    public function __construct(
        protected readonly Leader $model
    ) {}

    public function create(array $data): mixed
    {
        foreach ($data['user_id'] as $id) {
            Leader::query()->where('dept_id', $data['dept_id'])->where('user_id', $id)->forceDelete();
            Leader::create(['dept_id' => $data['dept_id'], 'user_id' => $id, 'created_at' => date('Y-m-d H:i:s')]);
        }
        // @phpstan-ignore-next-line
        return null;
    }

    public function deleteByDoubleKey(int $dept_id, array $user_ids): void
    {
        Leader::query()->where('dept_id', $dept_id)->whereIn('user_id', $user_ids)->forceDelete();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(isset($params['user_id']), static function (Builder $query) use ($params) {
                $query->where('user_id', $params['user_id']);
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
            ->with(['department', 'user']);
    }

    protected function enablePageOrderBy(): bool
    {
        return false;
    }
}
