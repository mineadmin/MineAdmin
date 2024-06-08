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

namespace App\System\Mapper;

use App\System\Model\SystemDept;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;
use Mine\Exception\MineException;
use Mine\MineCollection;

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
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        $treeData = $this->model::query()->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', $this->model::ENABLE)
            ->orderBy('parent_id')
            ->orderBy('sort', 'desc')
            ->userDataScope()
            ->get()->toArray();

        $deptTree = (new MineCollection())->toTree($treeData, $treeData[0]['parent_id'] ?? 0);

        if (config('mineadmin.data_scope_enabled', true) && ! user()->isSuperAdmin()) {
            $deptIds = Db::table(table: 'system_user_dept')->where('user_id', '=', user()->getId())->pluck('dept_id');
            $treeData = $this->model::query()
                ->select(['id', 'parent_id', 'id AS value', 'name AS label'])
                ->whereIn('id', $deptIds)
                ->where('status', $this->model::ENABLE)
                ->orderBy('parent_id')->orderBy('sort', 'desc')
                ->get()->toArray();

            // 去除重复部门
            $deptTree = array_merge($treeData, $deptTree);
            $deptTree = array_values(array_column($deptTree, null, 'id'));

            return (new MineCollection())->toTree($deptTree, $treeData[0]['parent_id'] ?? 0);
        }
        return $deptTree;
    }

    /**
     * 获取部门领导列表.
     */
    public function getLeaderList(?array $params = null): array
    {
        if (blank($params['dept_id'])) {
            throw new MineException('缺少部门ID', 500);
        }
        $query = Db::table('system_user as u')
            ->join('system_dept_leader as dl', 'u.id', '=', 'dl.user_id')
            ->where('dl.dept_id', '=', $params['dept_id']);

        if (isset($params['username']) && filled($params['username'])) {
            $query->where('u.username', 'like', '%' . $params['username'] . '%');
        }

        if (isset($params['nickname']) && filled($params['nickname'])) {
            $query->where('u.nickname', 'like', '%' . $params['nickname'] . '%');
        }

        if (isset($params['status']) && filled($params['status'])) {
            $query->where('u.status', $params['status']);
        }

        return $this->setPaginate(
            $query->paginate(
                (int) ($params['pageSize'] ?? $this->model::PAGE_SIZE),
                ['u.*', 'dl.created_at as leader_add_time'],
                'page',
                (int) ($params['page'] ?? 1)
            )
        );
    }

    /**
     * 新增部门领导
     */
    #[Transaction]
    public function addLeader(int $id, array $users): bool
    {
        $model = $this->model::find($id, ['id']);
        foreach ($users as $key => $user) {
            if (Db::table('system_dept_leader')->where('dept_id', $id)->where('user_id', $user['user_id'])->exists()) {
                unset($users[$key]);
            }
        }
        count($users) > 0 && $model->leader()->sync($users, false);
        return true;
    }

    /**
     * 删除部门领导
     */
    #[Transaction]
    public function delLeader(int $id, array $users): bool
    {
        $model = $this->model::find($id, ['id']);
        count($users) > 0 && $model->leader()->detach($users);
        return true;
    }

    /**
     * 查询部门名称.
     */
    public function getDeptName(?array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['leader']) && filled($params['leader'])) {
            $query->where('leader', $params['leader']);
        }

        if (isset($params['phone']) && filled($params['phone'])) {
            $query->where('phone', $params['phone']);
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }
        return $query;
    }
}
