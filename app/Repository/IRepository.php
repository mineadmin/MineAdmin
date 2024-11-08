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

namespace App\Repository;

use App\Repository\Traits\BootTrait;
use App\Repository\Traits\RepositoryOrderByTrait;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Hyperf\DbConnection\Traits\HasContainer;
use Hyperf\Paginator\AbstractPaginator;

/**
 * @template T of Model
 * @property T $model
 */
abstract class IRepository
{
    use BootTrait;
    use HasContainer;
    use RepositoryOrderByTrait;

    public const PER_PAGE_PARAM_NAME = 'per_page';

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }

    public function handleItems(Collection $items): Collection
    {
        return $items;
    }

    public function handlePage(array $page): array
    {
        return $page;
    }

    public function list(array $params = []): Collection
    {
        return $this->perQuery($this->getQuery($params), $params)->get();
    }

    public function count(array $params = []): int
    {
        return $this->perQuery($this->getQuery($params), $params)->count();
    }

    public function page(array $params = [], ?int $page = null, ?int $pageSize = null): array
    {
        $result = $this->perQuery($this->getQuery($params), $params)->paginate(
            perPage: $pageSize,
            pageName: static::PER_PAGE_PARAM_NAME,
            page: $page,
        );
        if ($result instanceof AbstractPaginator) {
            $items = $result->getCollection();
        } else {
            $items = Collection::make($result->items());
        }
        $items = $this->handleItems($items);
        return $this->handlePage([
            'list' => $items->toArray(),
            'total' => $result->total(),
        ]);
    }

    /**
     * @return T
     */
    public function create(array $data): mixed
    {
        // @phpstan-ignore-next-line
        return $this->getQuery()->create($data);
    }

    public function updateById(mixed $id, array $data): bool
    {
        return (bool) $this->getQuery()->whereKey($id)->first()?->update($data);
    }

    /**
     * @return null|T
     */
    public function saveById(mixed $id, array $data): mixed
    {
        $model = $this->getQuery()->whereKey($id)->first();
        if ($model) {
            $model->fill($data)->save();
            return $model;
        }
        return null;
    }

    public function deleteById(mixed $id): int
    {
        // @phpstan-ignore-next-line
        return $this->model::destroy($id);
    }

    public function forceDeleteById(mixed $id): bool
    {
        return (bool) $this->getQuery()->whereKey($id)->forceDelete();
    }

    /**
     * @return null|T
     */
    public function findById(mixed $id): mixed
    {
        return $this->getQuery()->whereKey($id)->first();
    }

    public function findByField(mixed $id, string $column): mixed
    {
        return $this->getQuery()->whereKey($id)->value($column);
    }

    /**
     * @return null|T
     */
    public function findByFilter(array $params): mixed
    {
        return $this->perQuery($this->getQuery($params), $params)->first();
    }

    public function perQuery(Builder $query, array $params): Builder
    {
        $this->startBoot($query, $params);
        return $this->handleSearch($query, $params);
    }

    public function getQuery(array $params = []): Builder
    {
        return $this->model->newQuery();
    }

    public function existsById(mixed $id): bool
    {
        return (bool) $this->getQuery()->whereKey($id)->exists();
    }

    /**
     * @return T
     */
    public function getModel()
    {
        return $this->model;
    }
}
