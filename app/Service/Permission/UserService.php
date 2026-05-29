<?php

namespace App\Service\Permission;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function menus(User $user): mixed
    {
        return $user->isSuperAdmin() ? $this->superAdminMenus() : $this->userMenus($user);
    }

    public function roles(User $user): Collection
    {
        return $user->isSuperAdmin()
            ? Role::query()->where('status', Status::Normal)->orderBy('sort')->get()
            : $user->getRoles(['name', 'code', 'remark']);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    public function page(array $params, int $page, int $pageSize, User $currentUser): array
    {
        $paginator = User::query()
            ->dataPermission($currentUser)
            ->when(Arr::get($params, 'unique_username'), function ($query, string $uniqueUsername): void {
                $query->where('username', $uniqueUsername);
            })
            ->when(Arr::get($params, 'username'), function ($query, string $username): void {
                $query->where('username', 'like', '%'.$username.'%');
            })
            ->when(Arr::get($params, 'phone'), function ($query, string $phone): void {
                $query->where('phone', $phone);
            })
            ->when(Arr::get($params, 'email'), function ($query, string $email): void {
                $query->where('email', $email);
            })
            ->when(Arr::has($params, 'status'), function ($query) use ($params): void {
                $query->where('status', $params['status']);
            })
            ->when(Arr::has($params, 'user_type'), function ($query) use ($params): void {
                $query->where('user_type', $params['user_type']);
            })
            ->when(Arr::has($params, 'nickname'), function ($query) use ($params): void {
                $query->where('nickname', 'like', '%'.Arr::get($params, 'nickname').'%');
            })
            ->when(Arr::get($params, 'created_at'), function ($query, array $createdAt): void {
                $query->whereBetween('created_at', [
                    $createdAt[0].' 00:00:00',
                    $createdAt[1].' 23:59:59',
                ]);
            })
            ->when(Arr::get($params, 'user_ids'), function ($query, mixed $userIds): void {
                $query->whereIn('id', Arr::wrap($userIds));
            })
            ->when(Arr::get($params, 'role_id'), function ($query, mixed $roleId): void {
                $query->whereHas('roles', function ($query) use ($roleId): void {
                    $query->where('role_id', $roleId);
                });
            })
            ->when(Arr::get($params, 'department_id'), function ($query, mixed $departmentId): void {
                $query->where(function ($query) use ($departmentId): void {
                    $query->whereHas('department', function ($query) use ($departmentId): void {
                        $query->where('id', $departmentId);
                    })->orWhereHas('deptLeader', function ($query) use ($departmentId): void {
                        $query->where('id', $departmentId);
                    })->orWhereHas('position.department', function ($query) use ($departmentId): void {
                        $query->where('id', $departmentId);
                    });
                });
            })
            ->with(['policy', 'department', 'deptLeader', 'position'])
            ->orderBy($this->orderBy($params), $this->orderDirection($params))
            ->paginate(perPage: $pageSize, page: $page);

        return $this->formatPage($paginator);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(User $user, array $data): void
    {
        if (Arr::exists($data, 'new_password')) {
            if (! $user->verifyPassword((string) Arr::get($data, 'old_password'))) {
                throw new BusinessException(ResultCode::Unprocessable, trans('user.old_password_error'));
            }

            $data['password'] = $data['new_password'];
        }

        unset($data['old_password'], $data['new_password'], $data['new_password_confirmation']);
        $user->fill($data)->save();
    }

    public function resetPassword(?int $id): bool
    {
        if ($id === null) {
            return false;
        }

        $user = $this->findById($id);
        $user->resetPassword();
        $user->save();

        return true;
    }

    public function getUserRole(int $id): Collection
    {
        return $this->findById($id)->roles()->get();
    }

    /**
     * @param  array<int, string>  $roleCodes
     */
    public function batchGrantRoleForUser(int $id, array $roleCodes): void
    {
        $user = $this->findById($id);

        if ($roleCodes === []) {
            $user->roles()->detach();

            return;
        }

        $user->roles()->sync(
            Role::query()
                ->whereIn('code', $roleCodes)
                ->pluck('id')
                ->all(),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create($this->userData($data));
            $this->handleWith($user, $data);

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateById(int $id, array $data): void
    {
        DB::transaction(function () use ($id, $data): void {
            $user = $this->findById($id);
            $user->fill($this->userData($data))->save();
            $this->handleWith($user, $data);
        });
    }

    /**
     * @param  array<int, int>|int  $ids
     */
    public function deleteById(array|int $ids): void
    {
        User::destroy($ids);
    }

    private function findById(int $id): User
    {
        $user = User::query()->find($id);

        if ($user === null) {
            throw new BusinessException(ResultCode::NotFound, 'Not Found');
        }

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function handleWith(User $user, array $data): void
    {
        if (isset($data['department'])) {
            $user->department()->sync($data['department']);
        }

        if (isset($data['position'])) {
            $user->position()->sync($data['position']);
        }

        if (isset($data['policy'])) {
            $policy = $user->policy()->first();

            if ($policy !== null) {
                if (isset($data['policy']['id'])) {
                    $policy->fill($data['policy'])->save();
                } else {
                    $user->policy()->delete();
                }
            } elseif ($data['policy'] !== []) {
                $user->policy()->create($data['policy']);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function userData(array $data): array
    {
        return Arr::except($data, ['department', 'position', 'policy']);
    }

    private function superAdminMenus(): Collection
    {
        return Menu::query()
            ->where('status', Status::Normal)
            ->where('parent_id', 0)
            ->with('children')
            ->orderBy('sort')
            ->get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function userMenus(User $user): array
    {
        $menuIds = $user->roles()
            ->where('status', Status::Normal)
            ->with(['menus' => function ($query): void {
                $query->where('status', Status::Normal)->orderBy('sort');
            }])
            ->get()
            ->pluck('menus')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values();

        if ($menuIds->isEmpty()) {
            return [];
        }

        $menus = Menu::query()
            ->where('status', Status::Normal)
            ->whereIn('id', $menuIds)
            ->orderBy('sort')
            ->get()
            ->map(fn (Menu $menu): array => array_merge($menu->toArray(), ['children' => []]))
            ->all();

        return $this->menuTree($menus);
    }

    /**
     * @param  array<int, array<string, mixed>>  $menus
     * @return array<int, array<string, mixed>>
     */
    private function menuTree(array $menus): array
    {
        $tree = [];
        $map = [];

        foreach ($menus as $index => $menu) {
            $map[$menu['id']] = $index;
        }

        foreach ($menus as $index => $menu) {
            $parentId = (int) $menu['parent_id'];
            if ($parentId === 0 || ! array_key_exists($parentId, $map)) {
                $tree[] = &$menus[$index];
            } else {
                $menus[$map[$parentId]]['children'][] = &$menus[$index];
            }
        }

        return $tree;
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function orderBy(array $params): string
    {
        $orderBy = (string) Arr::get($params, 'order_by', 'id');
        $allowed = ['id', 'username', 'nickname', 'phone', 'email', 'status', 'user_type', 'created_at', 'updated_at'];

        return in_array($orderBy, $allowed, true) ? $orderBy : 'id';
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function orderDirection(array $params): string
    {
        return strtolower((string) Arr::get($params, 'order_by_direction')) === 'asc' ? 'asc' : 'desc';
    }

    /**
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    private function formatPage(LengthAwarePaginator $paginator): array
    {
        return [
            'list' => $paginator->items(),
            'total' => $paginator->total(),
        ];
    }
}
