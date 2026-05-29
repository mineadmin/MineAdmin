<?php

namespace App\Service\Permission;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\DataPermission\Policy;
use App\Models\Permission\Position;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class PositionService
{
    /**
     * @param  array<string, mixed>  $params
     * @return Collection<int, Position>
     */
    public function getList(array $params): Collection
    {
        return Position::query()
            ->when(Arr::get($params, 'name'), function ($query, string $name): void {
                $query->where('name', 'like', '%'.$name.'%');
            })
            ->when(Arr::get($params, 'dept_id'), function ($query, mixed $deptId): void {
                $query->where('dept_id', $deptId);
            })
            ->with(['department', 'policy'])
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Position
    {
        return Position::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateById(int $id, array $data): void
    {
        $this->findById($id)->fill($data)->save();
    }

    /**
     * @param  array<int, int>|int  $ids
     */
    public function deleteById(array|int $ids): void
    {
        Position::destroy($ids);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function batchDataPermission(int $id, array $data): void
    {
        $position = $this->findById($id);
        $policy = $position->policy()->first();

        $payload = [
            'position_id' => $position->id,
            'policy_type' => $data['policy_type'],
            'value' => Arr::get($data, 'value'),
        ];

        if ($policy instanceof Policy) {
            $policy->fill($payload)->save();

            return;
        }

        $position->policy()->create($payload);
    }

    private function findById(int $id): Position
    {
        $position = Position::query()->find($id);

        if ($position === null) {
            throw new BusinessException(ResultCode::NotFound, 'Not Found');
        }

        return $position;
    }
}
