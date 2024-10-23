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
use App\System\Model\SystemUser;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;
use Mine\MineModel;

/**
 * Class SystemUserMapper.
 */
class SystemUserMapper extends AbstractMapper
{
    /**
     * @var SystemUser
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemUser::class;
    }

    /**
     * 通过用户名检查用户.
     * @return Builder|Model
     */
    public function checkUserByUsername(string $username)
    {
        return $this->model::query()->where('username', $username)->firstOrFail();
    }

    /**
     * 通过用户名检查是否存在.
     */
    public function existsByUsername(string $username): bool
    {
        return $this->model::query()->where('username', $username)->exists();
    }

    /**
     * 检查用户密码
     */
    public function checkPass(string $password, string $hash): bool
    {
        return $this->model::passwordVerify($password, $hash);
    }

    /**
     * 新增用户.
     */
    #[Transaction]
    public function save(array $data): mixed
    {
        $role_ids = $data['role_ids'] ?? [];
        $post_ids = $data['post_ids'] ?? [];
        $dept_ids = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);

        $user = $this->model::create($data);
        $user->roles()->sync($role_ids, false);
        $user->posts()->sync($post_ids, false);
        $user->depts()->sync($dept_ids, false);
        return $user->id;
    }

    /**
     * 更新用户.
     */
    #[Transaction]
    public function update(mixed $id, array $data): bool
    {
        $role_ids = $data['role_ids'] ?? [];
        $post_ids = $data['post_ids'] ?? [];
        $dept_ids = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);

        $result = parent::update($id, $data);
        $user = $this->model::find($id);
        if ($user && $result) {
            ! empty($role_ids) && $user->roles()->sync($role_ids);
            ! empty($dept_ids) && $user->depts()->sync($dept_ids);
            $user->posts()->sync($post_ids);
            return true;
        }
        return false;
    }

    /**
     * 真实批量删除用户.
     */
    #[Transaction]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $user = $this->model::withTrashed()->find($id);
            if ($user) {
                $user->roles()->detach();
                $user->posts()->detach();
                $user->depts()->detach();
                $user->forceDelete();
            }
        }
        return true;
    }

    /**
     * 获取用户信息.
     */
    public function read(mixed $id, array $column = ['*']): ?MineModel
    {
        $user = $this->model::find($id);
        if ($user) {
            $user->setAttribute('roleList', $user->roles()->get(['id', 'name']) ?: []);
            $user->setAttribute('postList', $user->posts()->get(['id', 'name']) ?: []);
            $user->setAttribute('deptList', $user->depts()->get(['id', 'name']) ?: []);
        }
        return $user;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['dept_id']) && filled($params['dept_id']) && is_string($params['dept_id'])) {
            $deptIds = SystemDept::query()
                ->where(function ($query) use ($params) {
                    $query->where('id', '=', $params['dept_id'])
                        ->orWhere('level', 'like', $params['dept_id'] . ',%')
                        ->orWhere('level', 'like', '%,' . $params['dept_id'])
                        ->orWhere('level', 'like', '%,' . $params['dept_id'] . ',%');
                })
                ->pluck('id')
                ->toArray();
            $query->whereHas('depts', fn ($query) => $query->whereIn('id', $deptIds));
        }
        if (isset($params['username']) && filled($params['username'])) {
            $query->where('username', 'like', '%' . $params['username'] . '%');
        }
        if (isset($params['nickname']) && filled($params['nickname'])) {
            $query->where('nickname', 'like', '%' . $params['nickname'] . '%');
        }
        if (isset($params['phone']) && filled($params['phone'])) {
            $query->where('phone', '=', $params['phone']);
        }
        if (isset($params['email']) && filled($params['email'])) {
            $query->where('email', '=', $params['email']);
        }
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['filterSuperAdmin']) && filled($params['filterSuperAdmin'])) {
            $query->whereNotIn('id', [env('SUPER_ADMIN')]);
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }

        if (isset($params['userIds']) && filled($params['userIds'])) {
            $query->whereIn('id', $params['userIds']);
        }

        if (isset($params['showDept']) && filled($params['showDept'])) {
            $isAll = $params['showDeptAll'] ?? false;

            $query->with(['depts' => function ($query) use ($isAll) {
                /*
                 *  @var Builder $query
                 */
                $query->where('status', SystemDept::ENABLE);
                return $isAll ? $query->select(['*']) : $query->select(['id', 'name']);
            }]);
        }

        if (isset($params['role_id']) && filled($params['role_id'])) {
            $tablePrefix = env('DB_PREFIX');
            $query->whereRaw(
                "id IN ( SELECT user_id FROM {$tablePrefix}system_user_role WHERE role_id = ? )",
                [$params['role_id']]
            );
        }

        if (isset($params['post_id']) && filled($params['post_id'])) {
            $tablePrefix = env('DB_PREFIX');
            $query->whereRaw(
                "id IN ( SELECT user_id FROM {$tablePrefix}system_user_post WHERE post_id = ? )",
                [$params['post_id']]
            );
        }

        return $query;
    }

    /**
     * 初始化用户密码
     */
    public function initUserPassword(int $id, string $password): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->password = $password;
            return $model->save();
        }
        return false;
    }

    /**
     * 根据用户ID列表获取用户基础信息.
     */
    public function getUserInfoByIds(array $ids, ?array $select = null): array
    {
        if (! $select) {
            $select = ['id', 'username', 'nickname', 'phone', 'email', 'created_at'];
        }
        return $this->model::query()->whereIn('id', $ids)->select($select)->get()->toArray();
    }
}
