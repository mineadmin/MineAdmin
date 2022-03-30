<?php
declare (strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemMenu;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\DeleteCache;
use Mine\Annotation\Transaction;

class SystemMenuMapper extends AbstractMapper
{

    /**
     * @var SystemMenu
     */
    public $model;

    /**
     * 查询的菜单字段
     * @var string[]
     */
    public $menuField = [
        'id',
        'parent_id',
        'name',
        'code',
        'icon',
        'route',
        'is_hidden',
        'component',
        'redirect',
        'type'
    ];

    public function assignModel()
    {
        $this->model = SystemMenu::class;
    }

    /**
     * 获取超级管理员（创始人）的路由菜单
     * @return array
     */
    public function getSuperAdminRouters(): array
    {
        return $this->model::query()
            ->select(...$this->menuField)
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->get()->sysMenuToRouterTree();
    }

    /**
     * 通过菜单ID列表获取菜单数据
     * @param array $ids
     * @return array
     */
    public function getRoutersByIds(array $ids): array
    {
        return $this->model::query()
            ->whereIn('id', $ids)
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->select(...$this->menuField)
            ->get()->sysMenuToRouterTree();
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
            ->get()->toTree();
    }

    /**
     * 查询菜单code
     * @param array|null $ids
     * @return array
     */
    public function getMenuCode(array $ids = null): array
    {
        return $this->model::query()->whereIn('id', $ids)->pluck('code')->toArray();
    }

    /**
     * 通过 code 查询菜单名称
     * @param string $code
     * @return string
     */
    public function findNameByCode(string $code): ?string
    {
        return $this->model::query()->where('code', $code)->value('name');
    }

    /**
     * 单个或批量真实删除数据
     * @param array $ids
     * @return bool
     */
    #[DeleteCache("loginInfo:*"), Transaction]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $model->roles()->detach();
                $model->forceDelete();
            }
        }
        return true;
    }

    /**
     * 更新菜单
     * @param int $id
     * @param array $data
     * @return bool
     */
    #[DeleteCache("loginInfo:*")]
    public function update(int $id, array $data): bool
    {
        return parent::update($id, $data);
    }

    /**
     * 逻辑删除菜单
     * @param array $ids
     * @return bool
     */
    #[DeleteCache("loginInfo:*")]
    public function delete(array $ids): bool
    {
        return parent::delete($ids);
    }

    /**
     * 通过 route 查询菜单
     * @param string $route
     * @return Builder|Model|object|null
     */
    public function findMenuByRoute(string $route)
    {
        return $this->model::query()->where('route', $route)->first();
    }

    /**
     * 查询菜单code
     * @param array|null $ids
     * @return array
     */
    public function getMenuName(array $ids = null): array
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
        return $query;
    }
}