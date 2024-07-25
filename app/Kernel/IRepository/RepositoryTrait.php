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

namespace App\Kernel\IRepository;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Tappable\HigherOrderTapProxy;
use Mine\Exception\MineException;
use Mine\Exception\NormalStatusException;
use Mine\MineCollection;
use Mine\MineModel;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

trait RepositoryTrait
{
    /**
     * @var MineModel
     */
    public $model;

    /**
     * 获取列表数据.
     */
    public function getList(?array $params, bool $isScope = true): array
    {
        return $this->listQuerySetting($params, $isScope)->get()->toArray();
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params, bool $isScope = true, string $pageName = 'page'): array
    {
        $paginate = $this->listQuerySetting($params, $isScope)->paginate(
            (int) ($params['pageSize'] ?? $this->model::PAGE_SIZE),
            ['*'],
            $pageName,
            (int) ($params[$pageName] ?? 1)
        );
        return $this->setPaginate($paginate, $params);
    }

    /**
     * 远程通用列表查询
     * 主要服务于远程下拉菜单使用，其功能也支持单选、复选、级联选择等组件的使用，但下拉菜单组件适配最好。
     * 接口支持分页、不分页、模型关联、条件过滤、排序、分组，数据权限等一系列功能.
     *
     * 声明提示：该接口虽然方便快捷，但由于参数是前端传入，后端对接口仅在控制器做了登录检查，有一定的安全性影响，需谨慎使用。
     */
    public function getRemoteList(?array $params): array
    {
        if (! config('mineadmin.remote_api_enabled')) {
            throw new MineException('系统未启用【远程通用列表查询】', 500);
        }
        /* @var $model MineModel */
        $model = $this->getModel();
        $query = null;
        if (! empty($params['relations']) && is_array($params['relations'])) {
            foreach ($params['relations'] as $item) {
                $this->dynamicRelations($model, $item);
                /* @var $query Builder */
                $query = $model->with([$item['name'] => function ($query) use ($item) {
                    $paramsWhere = [];
                    if (! empty($item['filter']) && is_array($item['filter'])) {
                        foreach ($item['filter'] as $name => $where) {
                            $paramsWhere[$name] = $where;
                        }
                    }
                    return $this->emptyBuildQuery($paramsWhere, $query);
                }]);
            }
        }

        /* @var $query Builder */
        if (is_null($query)) {
            $query = $model::query();
        }

        $paramsWhere = [];
        if (! empty($params['filter']) && is_array($params['filter'])) {
            foreach ($params['filter'] as $name => $where) {
                $paramsWhere[$name] = $where;
            }
        }
        $query = $this->emptyBuildQuery($paramsWhere, $query);

        if (! empty($params['sort']) && is_array($params['sort'])) {
            foreach ($params['sort'] as $name => $sortType) {
                $query->orderBy($name, $sortType);
            }
        }

        if (! empty($params['group']) && is_array($params['group'])) {
            foreach ($params['group'] as $name) {
                $query->groupBy($name);
            }
        }

        if (isset($params['dataScope']) && $params['dataScope']) {
            $query->userDataScope();
        }

        if (isset($params['openPage']) && $params['openPage']) {
            $pageName = $params['pageName'] ?? 'page';
            $pageSize = $params['pageSize'] ?? $this->model::PAGE_SIZE;
            return $this->setPaginate($query->paginate($pageSize, $params['select'] ?? ['*'], $pageName, $params[$pageName] ?? 1), $params);
        }

        return method_exists($this, 'handleItems')
            ? (new MineCollection($this->handleItems($query->get($params['select'] ?? ['*']), $params)))->toArray()
            : $query->get($params['select'] ?? ['*'])->toArray();
    }

    /**
     * 设置数据库分页.
     */
    public function setPaginate(LengthAwarePaginatorInterface $paginate, array $params = []): array
    {
        return [
            'items' => method_exists($this, 'handlePageItems') ? $this->handlePageItems($paginate->items(), $params) : $paginate->items(),
            'pageInfo' => [
                'total' => $paginate->total(),
                'currentPage' => $paginate->currentPage(),
                'totalPage' => $paginate->lastPage(),
            ],
        ];
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(
        ?array $params = null,
        bool $isScope = true,
        string $id = 'id',
        string $parentField = 'parent_id',
        string $children = 'children'
    ): array {
        $params['_mineadmin_tree'] = true;
        $params['_mineadmin_tree_pid'] = $parentField;
        $data = $this->listQuerySetting($params, $isScope)->get();
        return $data->toTree([], $data[0]->{$parentField} ?? 0, $id, $parentField, $children);
    }

    /**
     * 返回模型查询构造器.
     */
    public function listQuerySetting(?array $params, bool $isScope): Builder
    {
        $query = (($params['recycle'] ?? false) === true) ? $this->model::onlyTrashed() : $this->model::query();

        if ($params['select'] ?? false) {
            $query->select($this->filterQueryAttributes($params['select']));
        }

        $query = $this->handleOrder($query, $params);

        $isScope && $query->userDataScope();

        return $this->handleSearch($query, $params);
    }

    /**
     * 排序处理器.
     */
    public function handleOrder(Builder $query, ?array &$params = null): Builder
    {
        // 对树型数据强行加个排序
        if (isset($params['_mineadmin_tree'])) {
            $query->orderBy($params['_mineadmin_tree_pid']);
        }

        if ($params['orderBy'] ?? false) {
            if (is_array($params['orderBy'])) {
                foreach ($params['orderBy'] as $key => $order) {
                    $query->orderBy($order, $params['orderType'][$key] ?? 'asc');
                }
            } else {
                $query->orderBy($params['orderBy'], $params['orderType'] ?? 'asc');
            }
        }

        return $query;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }

    /**
     * 过滤查询字段不存在的属性.
     */
    public function filterQueryAttributes(array $fields, bool $removePk = false): array
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($fields as $key => $field) {
            if (! in_array(trim($field), $attrs) && mb_strpos(str_replace('AS', 'as', $field), 'as') === false) {
                unset($fields[$key]);
            } else {
                $fields[$key] = trim($field);
            }
        }
        if ($removePk && in_array($model->getKeyName(), $fields)) {
            unset($fields[array_search($model->getKeyName(), $fields)]);
        }
        $model = null;
        return (count($fields) < 1) ? ['*'] : $fields;
    }

    /**
     * 过滤新增或写入不存在的字段.
     */
    public function filterExecuteAttributes(array &$data, bool $removePk = false): void
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($data as $name => $val) {
            if (! in_array($name, $attrs)) {
                unset($data[$name]);
            }
        }
        if ($removePk && isset($data[$model->getKeyName()])) {
            unset($data[$model->getKeyName()]);
        }
        $model = null;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    /**
     * 读取一条数据.
     */
    public function read(mixed $id, array $column = ['*']): ?MineModel
    {
        return ($model = $this->model::find($id, $column)) ? $model : null;
    }

    /**
     * 按条件读取一行数据.
     * @return mixed
     */
    public function first(array $condition, array $column = ['*']): ?MineModel
    {
        return ($model = $this->model::where($condition)->first($column)) ? $model : null;
    }

    /**
     * 获取单个值
     * @return null|HigherOrderTapProxy|mixed|void
     */
    public function value(array $condition, string $columns = 'id')
    {
        return ($model = $this->model::where($condition)->value($columns)) ? $model : null;
    }

    /**
     * 获取单列值
     */
    public function pluck(array $condition, string $columns = 'id', ?string $key = null): array
    {
        return $this->model::where($condition)->pluck($columns, $key)->toArray();
    }

    /**
     * 从回收站读取一条数据.
     * @noinspection PhpUnused
     */
    public function readByRecycle(mixed $id): ?MineModel
    {
        return ($model = $this->model::withTrashed()->find($id)) ? $model : null;
    }

    /**
     * 单个或批量软删除数据.
     */
    public function delete(array $ids): bool
    {
        $this->model::destroy($ids);
        return true;
    }

    /**
     * 更新一条数据.
     */
    public function update(mixed $id, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::find($id)->update($data) > 0;
    }

    /**
     * 按条件更新数据.
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::query()->where($condition)->update($data) > 0;
    }

    /**
     * 单个或批量真实删除数据.
     */
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            $model && $model->forceDelete();
        }
        return true;
    }

    /**
     * 单个或批量从回收站恢复数据.
     */
    public function recovery(array $ids): bool
    {
        $this->model::withTrashed()->whereIn((new $this->model())->getKeyName(), $ids)->restore();
        return true;
    }

    /**
     * 单个或批量禁用数据.
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model())->getKeyName(), $ids)->update([$field => $this->model::DISABLE]);
        return true;
    }

    /**
     * 单个或批量启用数据.
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model())->getKeyName(), $ids)->update([$field => $this->model::ENABLE]);
        return true;
    }

    public function getModel(): MineModel
    {
        return new $this->model();
    }

    /**
     * 数据导入.
     * @throws Exception
     */
    public function import(string $dto, ?\Closure $closure = null): bool
    {
        return Db::transaction(function () use ($dto, $closure) {
            return (new MineCollection())->import($dto, $this->getModel(), $closure);
        });
    }

    /**
     * 闭包通用查询设置.
     * @param null|\Closure $closure 传入的闭包查询
     */
    public function settingClosure(?\Closure $closure = null): Builder
    {
        return $this->model::where(function ($query) use ($closure) {
            if ($closure instanceof \Closure) {
                $closure($query);
            }
        });
    }

    /**
     * 闭包通用方式查询一条数据.
     * @param array|string[] $column
     * @return null|Builder|Model
     */
    public function one(?\Closure $closure = null, array $column = ['*'])
    {
        return $this->settingClosure($closure)->select($column)->first();
    }

    /**
     * 闭包通用方式查询数据集合.
     * @param array|string[] $column
     */
    public function get(?\Closure $closure = null, array $column = ['*']): array
    {
        return $this->settingClosure($closure)->get($column)->toArray();
    }

    /**
     * 闭包通用方式统计
     */
    public function count(?\Closure $closure = null, string $column = '*'): int
    {
        return $this->settingClosure($closure)->count($column);
    }

    /**
     * 闭包通用方式查询最大值
     * @return mixed|string|void
     */
    public function max(?\Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->max($column);
    }

    /**
     * 闭包通用方式查询最小值
     * @return mixed|string|void
     */
    public function min(?\Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->min($column);
    }

    /**
     * 数字更新操作.
     */
    public function numberOperation(mixed $id, string $field, int $value): bool
    {
        return $this->update($id, [$field => $value]);
    }

    /**
     * 搜索参数注入.
     * @param mixed $params
     */
    public function paramsEmptyQuery($params, array $where = [], mixed $query = null): mixed
    {
        if (! $query) {
            $query = $this->model::query();
        }

        $object = new class($params, $where) {
            public array $paramsWhere = [];

            public function __construct($params, $where)
            {
                foreach ($params as $field => $value) {
                    if (isset($where[$field])) {
                        $this->caseWhere($field, $where[$field], $value);
                    }
                }
            }

            public function caseWhere($field, $operator, $value): void
            {
                if (is_scalar($operator)) {
                    $res = $this->scalarOptionHandle($field, $operator, $value);
                } elseif (is_array($operator)) {
                    $res = $this->arrayOptionHandle($field, $operator, $value);
                } else {
                    $res = $this->scalarOptionHandle($field, $operator, $value);
                }
                $this->paramsWhere[$res[0]] = [$res[1], $res[2]];
            }

            /**
             * 标量类型获取.
             * @param mixed $field
             * @param mixed $operator
             * @param mixed $value
             */
            public function scalarOptionHandle($field, $operator, $value): array
            {
                return [$field, $operator, $value];
            }

            /**
             * 数组类型处理.
             * @param mixed $field
             * @param mixed $operator
             * @param mixed $value
             */
            public function arrayOptionHandle($field, $operator, $value): array
            {
                return [$field, $operator, $value];
            }

            public function getParamsWhere(): array
            {
                return $this->paramsWhere;
            }
        };
        return $this->emptyBuildQuery($object->getParamsWhere(), $query);
    }

    /**
     * 非空查询方法
     * 案例
     * [
     *  'field' => 1,
     *  'field' => ['=', 'index']
     * ].
     */
    public function emptyBuildQuery(array $paramsWhere = [], mixed $query = null): mixed
    {
        if (! $query) {
            $query = $this->model::query();
        }
        $object = new class($paramsWhere, $query) {
            public mixed $query;

            public function __construct($paramsWhere, mixed $query)
            {
                $this->query = $query;
                foreach ($paramsWhere as $field => $value) {
                    if ($value) {
                        if (is_scalar($value)) {
                            $this->scalarWhere($field, '=', $value);
                        } elseif (is_array($value)) {
                            $this->arrayWhere($field, $value);
                        }
                    }
                }
            }

            public function scalarWhere($field, $operator, $value): void
            {
                [$operator, $value] = $this->optionHandler($field, $operator, $value);
                $this->query->where($field, $operator, $value);
            }

            private function arrayWhere($field, $value): void
            {
                [$value[0], $value[1]] = $this->optionHandler($field, $value[0], $value[1]);
                $this->query->where($field, $value[0], $value[1]);
            }

            public function optionHandler($field, $operator, $value): array
            {
                switch ($operator) {
                    case 'like':
                    case 'like%':
                        if (! is_scalar($value)) {
                            throw new NormalStatusException("{$field} type error:The expectation is a string");
                        }
                        $likeMap = ['like' => '%#{val}%', 'like%' => '#{val}%'];
                        $value = str_replace('#{val}', $value, $likeMap[$operator]);
                        break;
                }
                return [$operator, $value];
            }

            public function getQuery(): mixed
            {
                return $this->query;
            }
        };
        return $object->getQuery();
    }

    /**
     * 动态关联模型.
     * @param $config ['name', 'model', 'type', 'localKey', 'foreignKey', 'middleTable', 'as', 'where', 'whereIn' ]
     */
    public function dynamicRelations(MineModel $model, &$config): void
    {
        $model->resolveRelationUsing($config['name'], function ($primaryModel) use ($config) {
            $namespace = str_replace('.', '\\', $config['model']);
            if ($config['type'] === 'hasOne') {
                return $primaryModel->hasOne(new $namespace(), $config['foreignKey'], $config['localKey']);
            }
            if ($config['type'] === 'hasMany') {
                return $primaryModel->hasMany(new $namespace(), $config['foreignKey'], $config['localKey']);
            }
            if ($config['type'] === 'belongsTo') {
                return $primaryModel->belongsTo(new $namespace(), $config['foreignKey'], $config['localKey']);
            }
            if ($config['type'] === 'belongsToMany') {
                $primaryModel->belongsToMany(
                    new $namespace(),
                    $config['middleTable'],
                    $config['foreignKey'],
                    $config['localKey']
                );
                if (! empty($config['as'])) {
                    $primaryModel->as($config['as']);
                }
                if (! empty($config['where']) && is_array($config['where'])) {
                    foreach ($config['where'] as $field => $value) {
                        $primaryModel->wherePivot($field, $value);
                    }
                }
                if (! empty($config['whereIn']) && is_array($config['whereIn'])) {
                    foreach ($config['whereIn'] as $field => $value) {
                        $primaryModel->wherePivotIn($field, $value);
                    }
                }
            }
        });
    }
}
