<?php

declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemDept;
use App\System\Model\SystemUser;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\ModelNotFoundException;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;
use Mine\MineModel;

/**
 * Class SystemUserMapper
 * @package App\System\Mapper
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
     * 通过用户名检查用户
     * @param string $username
     * @return Builder|\Hyperf\Database\Model\Model
     */
    public function checkUserByUsername(string $username)
    {
        return $this->model::query()->where('username', $username)->firstOrFail();
    }

    /**
     * 通过用户名检查是否存在
     * @param string $username
     * @return bool
     */
    public function existsByUsername(string $username): bool
    {
        return $this->model::query()->where('username', $username)->exists();
    }

    /**
     * 检查用户密码
     * @param String $password
     * @param string $hash
     * @return bool
     */
    public function checkPass(String $password, string $hash): bool
    {
        return $this->model::passwordVerify($password, $hash);
    }

    /**
     * 新增用户
     * @param array $data
     * @return int
     */
    #[Transaction]
    public function save(array $data): int
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
     * 更新用户
     * @param int $id
     * @param array $data
     * @return bool
     */
    #[Transaction]
    public function update(int $id, array $data): bool
    {
        $role_ids = $data['role_ids'] ?? [];
        $post_ids = $data['post_ids'] ?? [];
        $dept_ids = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);

        $result = parent::update($id, $data);
        $user = $this->model::find($id);
        if ($user && $result) {
            !empty($role_ids) && $user->roles()->sync($role_ids);
            !empty($dept_ids) && $user->depts()->sync($dept_ids);
            $user->posts()->sync($post_ids);
            return true;
        }
        return false;
    }

    /**
     * 真实批量删除用户
     * @param array $ids
     * @return bool
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
     * 获取用户信息
     * @param int $id
     * @return MineModel
     */
    public function read(int $id): ?MineModel
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
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['dept_id']) && is_string($params['dept_id'])) {
            $query->join('system_user_dept as dept', 'system_user.id', '=', 'dept.user_id');
            $query->where('dept.dept_id', '=', $params['dept_id']);
        }
        if (isset($params['username'])) {
            $query->where('username', 'like', '%'.$params['username'].'%');
        }
        if (isset($params['nickname'])) {
            $query->where('nickname', 'like', '%'.$params['nickname'].'%');
        }
        if (isset($params['phone'])) {
            $query->where('phone', '=', $params['phone']);
        }
        if (isset($params['email'])) {
            $query->where('email', '=', $params['email']);
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['filterSuperAdmin'])) {
            $query->whereNotIn('id', [env('SUPER_ADMIN')]);
        }

        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59' ]
            );
        }

        if (isset($params['userIds'])) {
            $query->whereIn('id', $params['userIds']);
        }

        if (isset($params['showDept'])) {
            $isAll = $params['showDeptAll'] ?? false;

            $query->with(['depts' => function($query) use($isAll){
                /* @var Builder $query*/
                $query->where('status', SystemDept::ENABLE);
                return $isAll ? $query->select(['*']) : $query->select(['id', 'name']);
            }]);
        }

        if (isset($params['role_id'])) {
            $tablePrefix = env('DB_PREFIX');
            $query->whereRaw(
                "id IN ( SELECT user_id FROM {$tablePrefix}system_user_role WHERE role_id = ? )",
                [ $params['role_id'] ]
            );
        }

        if (isset($params['post_id'])) {
            $tablePrefix = env('DB_PREFIX');
            $query->whereRaw(
                "id IN ( SELECT user_id FROM {$tablePrefix}system_user_post WHERE post_id = ? )",
                [ $params['post_id'] ]
            );
        }

        return $query;
    }

    /**
     * 初始化用户密码
     * @param int $id
     * @param string $password
     * @return bool
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
     * 根据用户ID列表获取用户基础信息
     */
    public function getUserInfoByIds(array $ids, ?array $select = null): array
    {
        if (! $select) $select = ['id', 'username', 'nickname', 'phone', 'email', 'created_at'];
        return $this->model::query()->whereIn('id', $ids)->select($select)->get()->toArray();
    }
}