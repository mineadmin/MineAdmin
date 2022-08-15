<?php

declare(strict_types = 1);
namespace App\System\Mapper;


use App\System\Model\SystemDept;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class SystemDeptMapper extends AbstractMapper
{
    /**
     * @var SystemDept
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemDept::class;
    }

    /**
     * 获取前端选择树
     * @return array
     */
    public function getSelectTree(): array
    {
        return $this->model::query()->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->userDataScope()
            ->get()->toTree();
    }

    /**
     * 查询部门名称
     * @param array|null $ids
     * @return array
     */
    public function getDeptName(array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['name'])) {
            $query->where('name', 'like', '%'.$params['name'].'%');
        }

        if (isset($params['leader'])) {
            $query->where('leader', $params['leader']);
        }

        if (isset($params['phone'])) {
            $query->where('phone', $params['phone']);
        }

        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0] . '00:00:00', $params['created_at'][1] . '23:59:59' ]
            );
        }
        return $query;
    }
}