<?php

namespace App\Service\Permission;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class RoleService
{
    /**
     * @param  array<string, mixed>  $params
     * @return array{list: Role[], total: int}
     */
    public function page(array $params, int $page, int $pageSize): array
    {
        $paginator = Role::query()
            ->when(Arr::get($params, 'name'), function ($query, string $name): void {
                $query->where('name', 'like', '%'.$name.'%');
            })
            ->when(Arr::get($params, 'code'), function ($query, mixed $code): void {
                $query->whereIn('code', Arr::wrap($code));
            })
            ->when(Arr::has($params, 'status'), function ($query) use ($params): void {
                $query->where('status', $params['status']);
            })
            ->when(Arr::get($params, 'created_at'), function ($query, array $createdAt): void {
                $query->whereBetween('created_at', $createdAt);
            })
            ->orderBy((string) Arr::get($params, 'order_by', 'id'), (string) Arr::get($params, 'order_by_direction', 'desc'))
            ->paginate(perPage: $pageSize, page: $page);

        return $this->formatPage($paginator);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Role
    {
        return Role::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateById(int $id, array $data): void
    {
        Role::query()->whereKey($id)->first()?->update($data);
    }

    /**
     * @param  array<int, int>|int  $ids
     */
    public function deleteById(array|int $ids): void
    {
        Role::destroy($ids);
    }

    public function getRolePermission(int $id): Collection
    {
        return $this->findById($id)->menus()->get();
    }

    /**
     * @param  array<int, string>  $permissionsCode
     */
    public function batchGrantPermissionsForRole(int $id, array $permissionsCode): void
    {
        $role = $this->findById($id);

        if ($permissionsCode === []) {
            $role->menus()->detach();

            return;
        }

        $role->menus()->sync(
            Menu::query()
                ->whereIn('name', $permissionsCode)
                ->orderBy('sort')
                ->pluck('id')
                ->all(),
        );
    }

    private function findById(int $id): Role
    {
        $role = Role::query()->find($id);

        if ($role === null) {
            throw new BusinessException(ResultCode::NotFound, 'Not Found');
        }

        return $role;
    }

    /**
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    private function formatPage(LengthAwarePaginator $paginator): array
    {
        return [
            'list' => $paginator->getCollection()->toArray(),
            'total' => $paginator->total(),
        ];
    }
}
