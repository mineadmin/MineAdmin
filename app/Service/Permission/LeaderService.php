<?php

namespace App\Service\Permission;

use App\Models\Permission\Leader;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LeaderService
{
    /**
     * @param  array<string, mixed>  $params
     * @return Collection<int, Leader>
     */
    public function getList(array $params): Collection
    {
        return Leader::query()
            ->when(Arr::get($params, 'dept_id'), function ($query, mixed $deptId): void {
                $query->where('dept_id', $deptId);
            })
            ->when(Arr::get($params, 'user_id'), function ($query, mixed $userId): void {
                $query->where('user_id', $userId);
            })
            ->with(['department', 'user'])
            ->orderBy('dept_id')
            ->orderBy('user_id')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): void
    {
        DB::transaction(function () use ($data): void {
            foreach (Arr::get($data, 'user_id', []) as $userId) {
                DB::table('dept_leader')->updateOrInsert([
                    'dept_id' => $data['dept_id'],
                    'user_id' => $userId,
                ], [
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function deleteByDoubleKey(array $data): void
    {
        Leader::query()
            ->where('dept_id', Arr::get($data, 'dept_id'))
            ->whereIn('user_id', Arr::wrap(Arr::get($data, 'user_ids', [])))
            ->delete();
    }
}
