<?php

namespace Mine\Traits;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Utils\Collection;
use Mine\Annotation\Transaction;
use Mine\MineCollection;
use Mine\MineModel;

trait MapperTrait
{
    /**
     * @var MineModel
     */
    public $model;

    /**
     * 获取列表数据
     * @param array|null $params
     * @return array
     */
    public function getList(?array $params): array
    {
        return $this->listQuerySetting($params)->get()->toArray();
    }

    /**
     * 获取列表数据（带分页）
     * @param array|null $params
     * @param string $pageName
     * @return array
     */
    public function getPageList(?array $params, string $pageName = 'page'): array
    {
        $paginate = $this->listQuerySetting($params)->paginate(
            $params['pageSize'] ?? $this->model::PAGE_SIZE, ['*'], $pageName, $params[$pageName] ?? 1
        );
        return $this->setPaginate($paginate);
    }

    /**
     * 设置数据库分页
     * @param LengthAwarePaginatorInterface $paginate
     * @return array
     */
    public function setPaginate(LengthAwarePaginatorInterface $paginate): array
    {
        return [
            'items' => $paginate->items(),
            'pageInfo' => [
                'total' => $paginate->total(),
                'currentPage' => $paginate->currentPage(),
                'totalPage' => $paginate->lastPage()
            ]
        ];
    }

    /**
     * 获取树列表
     * @param array|null $params
     * @param string $id
     * @param string $parentField
     * @param string $children
     * @return array
     */
    public function getTreeList(
        ?array $params = null,
        string $id = 'id',
        string $parentField = 'parent_id',
        string $children='children'
    ): array
    {
        $params['_mainAdmin_tree'] = true;
        $params['_mainAdmin_tree_pid'] = $parentField;
        $data = $this->listQuerySetting($params)->get();
        return $data->toTree([], $data[0]->{$parentField} ?? 0, $id, $parentField, $children);
    }

    /**
     * 返回模型查询构造器
     * @param array|null $params
     * @return Builder
     */
    public function listQuerySetting(?array $params = null): Builder
    {
        $query = (($params['recycle'] ?? false) === true) ? $this->model::onlyTrashed() : $this->model::query();

        if ($params['select'] ?? false) {
            $query->select($this->filterQueryAttributes($params['select']));
        }

        // 对树型数据强行加个排序
        if (isset($params['_mainAdmin_tree'])) {
            $query->orderBy($params['_mainAdmin_tree_pid']);
        }

        if ($params['orderBy'] ?? false) {
            $query->orderBy($params['orderBy'], $params['orderType'] ?? 'asc');
        }

        $query->userDataScope();

        return $this->handleSearch($query, $params);
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }

    /**
     * 过滤查询字段不存在的属性
     * @param array $fields
     * @param bool $removePk
     * @return array
     */
    protected function filterQueryAttributes(array $fields, bool $removePk = false): array
    {
        $model = new $this->model;
        $attrs = $model->getFillable();
        foreach ($fields as $key => $field) {
            if (!in_array(trim($field), $attrs)) {
                unset($fields[$key]);
            } else {
                $fields[$key] = trim($field);
            }
        }
        if ($removePk && in_array($model->getKeyName(), $fields)) {
            unset($fields[array_search($model->getKeyName(), $fields)]);
        }
        $model = null;
        return ( count($fields) < 1 ) ? ['*'] : $fields;
    }

    /**
     * 过滤新增或写入不存在的字段
     * @param array $data
     * @param bool $removePk
     */
    protected function filterExecuteAttributes(array &$data, bool $removePk = false): void
    {
        $model = new $this->model;
        $attrs = $model->getFillable();
        foreach ($data as $name => $val) {
            if (!in_array($name, $attrs)) {
                unset($data[$name]);
            }
        }
        if ($removePk && isset($data[$model->getKeyName()])) {
            unset($data[$model->getKeyName()]);
        }
        $model = null;
    }

    /**
     * 新增数据
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        $this->filterExecuteAttributes($data);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    /**
     * 读取一条数据
     * @param int $id
     * @return MineModel
     */
    public function read(int $id): MineModel
    {
        return $this->model::find($id);
    }

    /**
     * 从回收站读取一条数据
     * @param int $id
     * @return MineModel
     * @noinspection PhpUnused
     */
    public function readByRecycle(int $id): MineModel
    {
        return $this->model::withTrashed()->find($id);
    }

    /**
     * 单个或批量软删除数据
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool
    {
        $this->model::destroy($ids);
        return true;
    }

    /**
     * 更新一条数据
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::query()->where((new $this->model)->getKeyName(), $id)->update($data);
    }

    /**
     * 单个或批量真实删除数据
     * @param array $ids
     * @return bool
     */
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            $model->forceDelete();
        }
        return true;
    }

    /**
     * 单个或批量从回收站恢复数据
     * @param array $ids
     * @return bool
     */
    public function recovery(array $ids): bool
    {
        $this->model::withTrashed()->whereIn((new $this->model)->getKeyName(), $ids)->restore();
        return true;
    }

    /**
     * 单个或批量禁用数据
     * @param array $ids
     * @param string $field
     * @return bool
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model)->getKeyName(), $ids)->update([$field => $this->model::DISABLE]);
        return true;
    }

    /**
     * 单个或批量启用数据
     * @param array $ids
     * @param string $field
     * @return bool
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model)->getKeyName(), $ids)->update([$field => $this->model::ENABLE]);
        return true;
    }

    /**
     * @return MineModel
     */
    public function getModel(): MineModel
    {
        return new $this->model;
    }

    /**
     * 数据导入
     * @param string $dto
     * @param \Closure|null $closure
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @Transaction
     */
    public function import(string $dto, ?\Closure $closure = null): bool
    {
        return (new MineCollection())->import($dto, $this->getModel(), $closure);
    }
}