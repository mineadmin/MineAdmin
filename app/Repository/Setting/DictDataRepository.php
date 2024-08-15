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

use App\Model\Setting\DictData;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

final class DictDataRepository extends IRepository
{
    public function __construct(
        protected readonly DictData $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'type_id'), function (Builder $query, array|string $type_id) {
                is_array($type_id) ? $query->whereIn('type_id', $type_id) : $query->where('type_id', $type_id);
            })
            ->when(Arr::get($params, 'code'), function (Builder $query, string $code) {
                $query->where('code', 'like', '%' . $code . '%');
            })
            ->when(Arr::get($params, 'value'), function (Builder $query, string $value) {
                $query->where('value', 'like', '%' . $value . '%');
            })
            ->when(Arr::get($params, 'label'), function (Builder $query, string $label) {
                $query->where('label', 'like', '%' . $label . '%');
            })
            ->when(Arr::get($params, 'status'), function (Builder $query, string $status) {
                $query->where('status', $status);
            });
    }

    public function deleteByDictTypeId(int $dictTypeId, bool $force = true): bool
    {
        $query = $this->model->newQuery()->where('type_id', $dictTypeId);
        return (bool) ($force ? $query->forceDelete() : $query->delete());
    }
}
