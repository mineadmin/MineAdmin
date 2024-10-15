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

namespace App\Repository;

use App\Model\UserOperationLog;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

final class UserOperationLogRepository extends IRepository
{
    public function __construct(
        protected readonly UserOperationLog $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        /*
         * @property string $username 用户名
         * @property string $method 请求方式
         * @property string $router 请求路由
         * @property string $service_name 业务名称
         * @property string $ip 请求IP地址
         * @property Carbon $created_at 创建时间
         * @property Carbon $updated_at 更新时间
         * @property Carbon $deleted_at 删除时间
         * @property string $remark 备注
         */
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
            })
            ->when(Arr::get($params, 'deleted_at'), static function (Builder $query, $deleted_at) {
                $query->whereBetween('deleted_at', $deleted_at);
            });
    }
}
