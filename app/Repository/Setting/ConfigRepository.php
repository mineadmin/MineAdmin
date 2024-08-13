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

use App\Model\Setting\Config;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class ConfigRepository extends IRepository
{
    public function __construct(
        protected readonly Config $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        // @phpstan-ignore-next-line
        return $query
            ->when(Arr::get($params, 'group_id'), function (Builder $query, $groupId) {
                return $query->where('group_id', $groupId);
            })
            ->when(Arr::get($params, 'key'), function (Builder $query, $key) {
                return $query->where('key', 'like', "{$key}%");
            })
            ->when(Arr::get($params, 'name'), function (Builder $query, $name) {
                return $query->where('name', 'name', "{$name}%");
            })
            ->when(Arr::get($params, 'input_type'), function (Builder $query, $inputType) {
                return $query->where('input_type', $inputType);
            })
            ->when(Arr::get($params, 'code'), function (Builder $query, $code) {
                return $query->where('code', $code);
            })
            ->orderBy('sort');
    }
}
