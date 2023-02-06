<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Mine\Traits;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Annotation\Transaction;
use Mine\MineCollection;
use Mine\MineModel;
use Hyperf\ModelCache\Manager;
use Hyperf\Utils\ApplicationContext;

trait MapperTrait
{
    /**
     * @var MineModel
     */
    public $model;

    /**
     * 获取列表数据
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getList(?array $params, bool $isScope = true): array
    {
        return $this->listQuerySetting($params, $isScope)->get()->toArray();
    }

    /**
     * 获取列表数据（带分页）
     * @param array|null $params
     * @param bool $isScope
     * @param string $pageName
     * @return array
     */
    public function getPageList(?array $params, bool $isScope = true, string $pageName = 'page'): array
    {
        $paginate = $this->listQuerySetting($params, $isScope)->paginate(
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
     * @param bool $isScope
     * @param string $id
     * @param string $parentField
     * @param string $children
     * @return array
     */
    public function getTreeList(
        ?array $params = null,
        bool $isScope = true,
        string $id = 'id',
        string $parentField = 'parent_id',
        string $children='children'
    ): array
    {
        $params['_mineadmin_tree'] = true;
        $params['_mineadmin_tree_pid'] = $parentField;
        $data = $this->listQuerySetting($params, $isScope)->get();
        return $data->toTree([], $data[0]->{$parentField} ?? 0, $id, $parentField, $children);
    }

    /**
     * 返回模型查询构造器
     * @param array|null $params
     * @param bool $isScope
     * @return Builder
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
     * 排序处理器
     * @param Builder $query
     * @param array|null $params
     * @return Builder
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
            if (!in_array(trim($field), $attrs) && mb_strpos(str_replace('AS', 'as', $field), 'as') === false) {
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
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    /**
     * 读取一条数据
     * @param int $id
     * @return MineModel|null
     */
    public function read(int $id): ?MineModel
    {
        return ($model = $this->model::find($id)) ? $model : null;
    }

    /**
     * 按条件读取一行数据
     * @param array $condition
     * @param array $column
     * @return mixed
     */
    public function first(array $condition, array $column = ['*']): ?MineModel
    {
        return ($model = $this->model::where($condition)->first($column)) ? $model : null;
    }

    /**
     * 获取单个值
     * @param array $condition
     * @param string $columns
     * @return \Hyperf\Utils\HigherOrderTapProxy|mixed|void|null
     */
    public function value(array $condition, string $columns = 'id')
    {
        return ($model = $this->model::where($condition)->value($columns)) ? $model : null;
    }

    /**
     * 获取单列值
     * @param array $condition
     * @param string $columns
     * @return array
     */
    public function pluck(array $condition, string $columns = 'id'): array
    {
        return $this->model::where($condition)->pluck($columns)->toArray();
    }

    /**
     * 从回收站读取一条数据
     * @param int $id
     * @return MineModel|null
     * @noinspection PhpUnused
     */
    public function readByRecycle(int $id): ?MineModel
    {
        return ($model = $this->model::withTrashed()->find($id)) ? $model : null;
    }

    /**
     * 单个或批量软删除数据
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool
    {
        $this->model::destroy($ids);

        $manager = ApplicationContext::getContainer()->get(Manager::class);
        $manager->destroy($ids,$this->model);

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
        return $this->model::find($id)->update($data) > 0;
    }

    /**
     * 按条件更新数据
     * @param array $condition
     * @param array $data
     * @return bool
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::query()->where($condition)->update($data) > 0;
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
            $model && $model->forceDelete();
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[Transaction]
    public function import(string $dto, ?\Closure $closure = null): bool
    {
        return (new MineCollection())->import($dto, $this->getModel(), $closure);
    }

    /**
     * 闭包通用查询设置
     * @param \Closure|null $closure 传入的闭包查询
     * @return Builder
     */
    public function settingClosure(?\Closure $closure = null): Builder
    {
        return $this->model::where(function($query) use($closure) {
            if ($closure instanceof \Closure) {
                $closure($query);
            }
        });
    }

    /**
     * 闭包通用方式查询一条数据
     * @param \Closure|null $closure
     * @param array|string[] $column
     * @return Builder|Model|null
     */
    public function one(?\Closure $closure = null, array $column = ['*'])
    {
        return $this->settingClosure($closure)->select($column)->first();
    }

    /**
     * 闭包通用方式查询数据集合
     * @param \Closure|null $closure
     * @param array|string[] $column
     * @return array
     */
    public function get(?\Closure $closure = null, array $column = ['*']): array
    {
        return $this->settingClosure($closure)->get($column)->toArray();
    }

    /**
     * 闭包通用方式统计
     * @param \Closure|null $closure
     * @param string $column
     * @return int
     */
    public function count(?\Closure $closure = null, string $column = '*'): int
    {
        return $this->settingClosure($closure)->count($column);
    }

    /**
     * 闭包通用方式查询最大值
     * @param \Closure|null $closure
     * @param string $column
     * @return mixed|string|void
     */
    public function max(?\Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->max($column);
    }

    /**
     * 闭包通用方式查询最小值
     * @param \Closure|null $closure
     * @param string $column
     * @return mixed|string|void
     */
    public function min(?\Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->min($column);
    }

    /**
     * 数字更新操作
     * @param int $id
     * @param string $field
     * @param int $value
     * @return bool
     */
    public function numberOperation(int $id, string $field, int $value): bool
    {
        return $this->update($id, [ $field => $value]);
    }
}
