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

use App\System\Model\SystemMenu;
use App\System\Model\SystemUser;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\DeleteCache;
use Mine\Annotation\Transaction;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SystemMenuMapper extends AbstractMapper
{
    /**
     * @var SystemMenu
     */
    public $model;

    /**
     * 查询的菜单字段.
     */
    public array $menuField = [
        'id',
        'parent_id',
        'name',
        'code',
        'icon',
        'route',
        'is_hidden',
        'component',
        'redirect',
        'type',
    ];

    public function assignModel()
    {
        $this->model = SystemMenu::class;
    }

    /**
     * 获取超级管理员（创始人）的路由菜单.
     */
    public function getSuperAdminRouters(): array
    {
        return $this->model::query()
            ->select($this->menuField)
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->get()->sysMenuToRouterTree();
    }

    /**
     * 通过菜单ID列表获取菜单数据.
     */
    public function getRoutersByIds(array $ids): array
    {
        return $this->model::query()
            ->select($this->menuField)
            ->whereIn('id', $ids)
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->get()->sysMenuToRouterTree();
    }

    /**
     * 获取前端选择树.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function getSelectTree(array $data): array
    {
        $query = $this->model::query()->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', $this->model::ENABLE)->orderBy('sort', 'desc');

        if (($data['scope'] ?? false) && ! user()->isSuperAdmin()) {
            $roleData = container()->get(SystemRoleMapper::class)->getMenuIdsByRoleIds(
                SystemUser::find(user()->getId(), ['id'])->roles()->pluck('id')->toArray()
            );

            $ids = [];
            foreach ($roleData as $val) {
                foreach ($val['menus'] as $menu) {
                    $ids[] = $menu['id'];
                }
            }
            unset($roleData);
            $query->whereIn('id', array_unique($ids));
        }

        if (! empty($data['onlyMenu'])) {
            $query->where('type', SystemMenu::MENUS_LIST);
        }

        return $query->get()->toTree();
    }

    /**
     * 查询菜单code.
     */
    public function getMenuCode(?array $ids = null): array
    {
        return $this->model::query()->whereIn('id', $ids)->pluck('code')->toArray();
    }

    /**
     * 通过 code 查询菜单名称.
     */
    public function findNameByCode(string $code): ?string
    {
        return $this->model::query()->where('code', $code)->value('name');
    }

    /**
     * 单个或批量真实删除数据.
     */
    #[DeleteCache('loginInfo:*'), Transaction]
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
     * 新增菜单.
     */
    #[DeleteCache('loginInfo:*')]
    public function save(array $data): mixed
    {
        return parent::save($data);
    }

    /**
     * 更新菜单.
     */
    #[DeleteCache('loginInfo:*')]
    public function update(mixed $id, array $data): bool
    {
        return parent::update($id, $data);
    }

    /**
     * 批量更新菜单.
     */
    #[DeleteCache('loginInfo:*'), Transaction]
    public function batchUpdate(array $update): bool
    {
        foreach ($update as $item) {
            $result = parent::update($item['id'], $item['data']);
            if (! $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 逻辑删除菜单.
     */
    #[DeleteCache('loginInfo:*')]
    public function delete(array $ids): bool
    {
        return parent::delete($ids);
    }

    /**
     * 通过 route 查询菜单.
     * @return null|Builder|Model|object
     */
    public function findMenuByRoute(string $route)
    {
        return $this->model::query()->where('route', $route)->first();
    }

    /**
     * 查询菜单code.
     */
    public function getMenuName(?array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 获取子孙menus.
     */
    public function getDescendantsMenus(int $parentId): array
    {
        $params = ['level' => $parentId];
        return $this->handleSearch($this->model::query(), $params)->get()->toArray();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['level']) && filled($params['level'])) {
            $query->where('level', 'like', '%' . $params['level'] . '%');
        }

        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }

        if (isset($params['noButton']) && filled($params['noButton']) && $params['noButton'] === true) {
            $query->where('type', '<>', SystemMenu::BUTTON);
        }
        return $query;
    }
}
