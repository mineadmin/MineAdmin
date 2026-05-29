<?php

namespace App\Service\Permission;

use App\Models\UserOperationLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class UserOperationLogService
{
    /**
     * @param  array{username: string, method: string, router: string, service_name: string, ip?: string|null, remark?: string|null}  $payload
     */
    public function record(array $payload): void
    {
        UserOperationLog::query()->create($payload);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    public function page(array $params, int $page, int $pageSize): array
    {
        $paginator = UserOperationLog::query()
            ->when(Arr::get($params, 'username'), function ($query, string $username): void {
                $query->where('username', 'like', '%'.$username.'%');
            })
            ->when(Arr::get($params, 'service_name'), function ($query, string $serviceName): void {
                $query->where('service_name', 'like', '%'.$serviceName.'%');
            })
            ->when(Arr::get($params, 'created_at'), function ($query, array $createdAt): void {
                $query->whereBetween('created_at', [
                    $createdAt[0].' 00:00:00',
                    $createdAt[1].' 23:59:59',
                ]);
            })
            ->orderBy('id', 'desc')
            ->paginate(perPage: $pageSize, page: $page);

        return $this->formatPage($paginator);
    }

    /**
     * @param  array<int, int>|int  $ids
     */
    public function deleteById(array|int $ids): void
    {
        UserOperationLog::destroy($ids);
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
