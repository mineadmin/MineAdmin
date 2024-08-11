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

namespace App\Repository\Setting;

use App\Model\Setting\DictType;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class DictTypeRepository extends IRepository
{
    public function __construct(
        protected readonly DictType $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'code'), function (Builder $query, string $code) {
                $query->where('code', 'like', '%' . $code . '%');
            })
            ->when(Arr::get($params, 'name'), function (Builder $query, string $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when(Arr::get($params, 'status'), function (Builder $query, string $status) {
                $query->where('status', $status);
            });
    }
}
