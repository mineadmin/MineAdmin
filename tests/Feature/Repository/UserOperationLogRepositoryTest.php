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

namespace HyperfTests\Feature\Repository;

use App\Model\UserOperationLog;
use App\Repository\Logstash\UserOperationLogRepository;
use Faker\Provider\Internet;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class UserOperationLogRepositoryTest extends AbstractTestRepository
{
    protected string $repositoryClass = UserOperationLogRepository::class;

    protected function getAttributes(): array
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
        return [
            'username' => Str::random(10),
            'method' => Str::random(4),
            'router' => Str::random(10),
            'service_name' => Str::random(10),
            'ip' => Internet::localIpv4(),
            'remark' => Str::random(),
        ];
    }

    /**
     * @param UserOperationLog $model
     */
    protected function getSearchAttributes(Model $model, Collection $entityList): array
    {
        return [
            'username' => $model->username,
            'method' => $model->method,
            'router' => $model->router,
            'service_name' => $model->service_name,
            'ip' => $model->ip,
            'created_at' => [$model->created_at->startOfDay(), $model->created_at->endOfDay()],
            'updated_at' => [$model->updated_at->startOfDay(), $model->updated_at->endOfDay()],
        ];
    }
}
