<?php

namespace App\Repository;

use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Hyperf\DbConnection\Traits\HasContainer;
use Hyperf\Paginator\AbstractPaginator;
use RuntimeException;
use function Hyperf\Collection\value;

/**
 * @template T
 * @property-read T|Model $model
 */
abstract class IRepository
{
    use HasContainer;

    public const PER_PAGE_PARAM_NAME = 'per_page';


    /**
     * @return T|Model
     */
    public function getModel()
    {
        if (!empty($this->model) && is_object($this->model)){
            return $this->model;
        }

        if (!empty($this->model) && class_exists($this->model) || interface_exists($this->model)) {
            return $this->getContainer()->get($this->model);
        }
        throw new RuntimeException(sprintf('Cannot detect the model of %s', static::class));
    }

    abstract function handleSearch(Builder $query,array $params): Builder;

    protected function getQuery(array $params = []): Builder
    {
        return value(function (Builder $builder, array $params){
            return $this->handleSearch($builder,$params);
        },$this->getModel()->newModelQuery(),$params);
    }

    public function perQuery(Builder $query):Builder
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

    public function list(array $params): Collection
    {
        return $this->perQuery($this->getQuery($params))->get();
    }

    public function page(array $params = [],?int $page = null,?int $pageSize = null): array
    {
        $result = $this->perQuery($this->getQuery($params))->paginate(
            perPage: $pageSize, pageName: static::PER_PAGE_PARAM_NAME, page: $page,
        );
        if ($result instanceof AbstractPaginator){
            $items = $result->getCollection();
        }else{
            $items = Collection::make($result->items());
        }
        $items = $this->handleItems($items);
        return $this->handlePage([
            'list'  =>  $items->toArray(),
            'total' =>  $result->total(),
        ]);
    }

    /**
     * @return T
     */
    public function create(array $data): mixed
    {
        return $this->getModel()::make($data)->save();
    }

    public function updateById(mixed $id,array $data):bool
    {
        return (bool)$this->getModel()::whereKey($id)->update($data);
    }

    /**
     * @return T|null
     */
    public function saveById(mixed $id,array $data): mixed
    {
        return value(function (Model $model,mixed $id,array $data){
            return $model->newModelQuery()->whereKey($id)->first()?->fill($data)->save();
        },$this->getModel(),$id,$data);
    }

    public function deleteById(mixed $id):bool
    {
        return (bool)$this->getModel()::whereKey($id)->delete();
    }

    public function forceDeleteById(mixed $id): bool
    {
        return $this->getModel()::whereKey($id)->forceDelete();
    }

    /**
     * @return null|T
     */
    public function findById(mixed $id): mixed
    {
        return $this->getModel()::whereKey($id)->first();
    }

}