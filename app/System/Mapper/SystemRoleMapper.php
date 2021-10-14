<?php
declare(strict_types = 1);
namespace App\System\Mapper;

use App\System\Model\SystemRole;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;

class SystemRoleMapper extends AbstractMapper
{
    /**
     * @var SystemRole
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemRole::class;
    }

    /**
     * 通过角色ID列表获取菜单ID
     * @param array $ids
     * @return array
     */
    public function getMenuIdsByRoleIds(array $ids): array
    {
        if (empty($ids)) return [];

        return $this->model::query()->whereIn('id', $ids)->with(['menus' => function($query) {
            $query->select('id')->where('status', $this->model::ENABLE)->orderBy('sort', 'desc');
        }])->get(['id'])->toArray();
    }

    /**
     * 通过角色ID列表获取部门ID
     * @param array $ids
     * @return array
     */
    public function getDeptIdsByRoleIds(array $ids): array
    {
        if (empty($ids)) return [];

        return $this->model::query()->whereIn('id', $ids)->with(['depts' => function($query) {
            $query->select('id')->where('status', $this->model::ENABLE)->orderBy('sort', 'desc');
        }])->get(['id'])->toArray();
    }

    /**
     * 检查角色code是否已存在
     * @param string $code
     * @return bool
     */
    public function checkRoleCode(string $code): bool
    {
        return $this->model::query()->where('code', $code)->exists();
    }

    /**
     * 新建角色
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        $menuIds = $data['menu_ids'] ?? [];
        $deptIds = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data);
        try {
            Db::beginTransaction();
            $role = $this->model::create($data);
            empty($menuIds) || $role->menus()->sync(array_unique($menuIds), false);
            empty($deptIds) || $role->depts()->sync($deptIds, false);
            Db::commit();
        } catch (\RuntimeException $e) {
            Db::rollBack();
            return 0;
        }
        return $role->id;
    }

    /**
     * 更新角色
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $menuIds = $data['menu_ids'] ?? [];
        $deptIds = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data);
        try {
            Db::beginTransaction();
            $this->model::query()->where('id', $id)->update($data);
            if ($id != env('ADMIN_ROLE')) {
                $role = $this->model::find($id);
                !empty($menuIds) && $role->menus()->sync(array_unique($menuIds));
                !empty($deptIds) && $role->depts()->sync($deptIds);
            }
            Db::commit();
        } catch (\RuntimeException $e) {
            Db::rollBack();
            return false;
        }
        return true;
    }

    /**
     * 单个或批量软删除数据
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool
    {
        $adminId = env('ADMIN_ROLE');
        if (in_array($adminId, $ids)) {
            unset($ids[array_search($adminId, $ids)]);
        }
        $this->model::destroy($ids);
        return true;
    }

    /**
     * 批量真实删除角色
     * @param array $ids
     * @return bool
     */
    public function realDelete(array $ids): bool
    {
        try {
            Db::beginTransaction();
            foreach ($ids as $id) {
                if ($id == env('ADMIN_ROLE')) {
                    continue;
                }
                $role = $this->model::withTrashed()->find($id);
                // 删除关联菜单
                $role->menus()->detach();
                // 删除关联部门
                $role->depts()->detach();
                // 删除关联用户
                $role->users()->detach();
                // 删除角色数据
                $role->forceDelete();
            }
            Db::commit();
        } catch (\RuntimeException $e) {
            Db::rollBack();
            return false;
        }
        return true;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['name'])) {
            $query->where('name', 'like', '%'.$params['name'].'%');
        }
        if (isset($params['code'])) {
            $query->where('code', $params['code']);
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['minDate']) && isset($params['maxDate'])) {
            $query->whereBetween(
                'created_at',
                [$params['minDate'] . ' 00:00:00', $params['maxDate'] . ' 23:59:59']
            );
        }
        return $query;
    }
}