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

namespace App\Repository\Traits;

use Hyperf\Database\Model\Builder;

trait RepositoryOrderByTrait
{
    public function handleOrderBy(Builder $query, $params): Builder
    {
        if ($this->enablePageOrderBy()) {
            $orderByField = $params[$this->getOrderByParamName()] ?? $query->getModel()->getKeyName();
            $orderByDirection = $params[$this->getOrderByDirectionParamName()] ?? 'desc';
            $query->orderBy($orderByField, $orderByDirection);
        }
        return $query;
    }

    protected function bootRepositoryOrderByTrait(Builder $query, array $params): void
    {
        $this->handleOrderBy($query, $params);
    }

    protected function getOrderByParamName(): string
    {
        return 'order_by';
    }

    protected function getOrderByDirectionParamName(): string
    {
        return 'order_by_direction';
    }

    protected function enablePageOrderBy(): bool
    {
        return true;
    }
}
