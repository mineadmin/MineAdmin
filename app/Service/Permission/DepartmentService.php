<?php

namespace App\Service\Permission;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\Permission\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    /**
     * @param  array<string, mixed>  $params
     * @return Collection<int, Department>
     */
    public function getList(array $params): Collection
    {
        return Department::query()
            ->when(Arr::get($params, 'name'), function ($query, string $name): void {
                $query->where('name', 'like', '%'.$name.'%');
            })
            ->where('parent_id', 0)
            ->with(['children', 'positions', 'departmentUsers', 'leader'])
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Department
    {
        return DB::transaction(function () use ($data): Department {
            $department = Department::query()->create($this->departmentData($data));
            $this->syncRelations($department, $data);

            return $department;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateById(int $id, array $data): void
    {
        DB::transaction(function () use ($id, $data): void {
            $department = $this->findById($id);
            $department->fill($this->departmentData($data))->save();
            $this->syncRelations($department, $data);
        });
    }

    /**
     * @param  array<int, int>|int  $ids
     */
    public function deleteById(array|int $ids): void
    {
        Department::destroy($ids);
    }

    private function findById(int $id): Department
    {
        $department = Department::query()->find($id);

        if ($department === null) {
            throw new BusinessException(ResultCode::NotFound, 'Not Found');
        }

        return $department;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function departmentData(array $data): array
    {
        return Arr::except($data, ['department_users', 'leader']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncRelations(Department $department, array $data): void
    {
        if (array_key_exists('department_users', $data)) {
            $department->departmentUsers()->sync(Arr::get($data, 'department_users', []));
        }

        if (array_key_exists('leader', $data)) {
            $department->leader()->sync(Arr::get($data, 'leader', []));
        }
    }
}
