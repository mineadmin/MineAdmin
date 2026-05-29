<?php

namespace App\Service\Permission;

use App\Models\UserLoginLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class UserLoginLogService
{
    /**
     * @param  array<string, mixed>  $params
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    public function page(array $params, int $page, int $pageSize): array
    {
        $paginator = UserLoginLog::query()
            ->when(Arr::get($params, 'username'), function ($query, string $username): void {
                $query->where('username', 'like', '%'.$username.'%');
            })
            ->when(Arr::get($params, 'ip'), function ($query, string $ip): void {
                $query->where('ip', $ip);
            })
            ->when(Arr::get($params, 'os'), function ($query, string $os): void {
                $query->where('os', $os);
            })
            ->when(Arr::get($params, 'browser'), function ($query, string $browser): void {
                $query->where('browser', $browser);
            })
            ->when(Arr::has($params, 'status'), function ($query) use ($params): void {
                $query->where('status', $params['status']);
            })
            ->when(Arr::get($params, 'message'), function ($query, string $message): void {
                $query->where('message', $message);
            })
            ->when(Arr::get($params, 'remark'), function ($query, string $remark): void {
                $query->where('remark', $remark);
            })
            ->when(Arr::get($params, 'login_time'), function ($query, array $loginTime): void {
                $query->whereBetween('login_time', $loginTime);
            })
            ->when(Arr::get($params, 'created_at'), function ($query, array $createdAt): void {
                $query->whereBetween('login_time', [
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
        UserLoginLog::destroy($ids);
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
