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

use App\Model\Setting\ConfigGroup;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class ConfigGroupRepository extends IRepository
{
    public function __construct(
        protected readonly ConfigGroup $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        // @phpstan-ignore-next-line
        return $query
            ->when(Arr::get($params, 'name'), function (Builder $query, $name) {
                return $query->where('name', 'like', "{$name}%");
            })
            ->when(Arr::get($params, 'code'), function (Builder $query, $code) {
                return $query->where('code', $code);
            })
            ->orderBy('id', 'desc');
    }
}
