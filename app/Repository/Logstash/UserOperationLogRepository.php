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

namespace App\Repository\Logstash;

use App\Model\UserOperationLog;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

final class UserOperationLogRepository extends IRepository
{
    public function __construct(
        protected readonly UserOperationLog $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'username'), static function (Builder $query, $username) {
                $query->where('username', $username);
            })
            ->when(Arr::get($params, 'method'), static function (Builder $query, $method) {
                $query->where('method', $method);
            })
            ->when(Arr::get($params, 'router'), static function (Builder $query, $router) {
                $query->where('router', $router);
            })
            ->when(Arr::get($params, 'service_name'), static function (Builder $query, $service_name) {
                $query->where('service_name', $service_name);
            })
            ->when(Arr::get($params, 'ip'), static function (Builder $query, $ip) {
                $query->where('ip', $ip);
            })
            ->when(Arr::get($params, 'created_at'), static function (Builder $query, $created_at) {
                $query->whereBetween('created_at', $created_at);
            })
            ->when(Arr::get($params, 'updated_at'), static function (Builder $query, $updated_at) {
                $query->whereBetween('updated_at', $updated_at);
            });
    }
}
