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

use App\Model\UserLoginLog;
use App\Repository\Logstash\UserLoginLogRepository;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class UserLoginLogRepositoryTest extends AbstractTestRepository
{
    /**
     * @property int $id 主键
     * @property string $username 用户名
     * @property string $ip 登录IP地址
     * @property string $os 操作系统
     * @property string $browser 浏览器
     * @property int $status 登录状态 (1成功 2失败)
     * @property string $message 提示消息
     * @property Carbon $login_time 登录时间
     * @property string $remark 备注
     */
    protected string $repositoryClass = UserLoginLogRepository::class;

    protected function getAttributes(): array
    {
        return [
            'username' => Str::random(10),
            'ip' => '127.0.0.1',
            'os' => Str::random(10),
            'browser' => Str::random(10),
            'status' => 1,
            'message' => Str::random(10),
            'login_time' => Carbon::now(),
            'remark' => Str::random(10),
        ];
    }

    /**
     * @param UserLoginLog $model
     */
    protected function getSearchAttributes(Model $model, Collection $entityList): array
    {
        return [
            'username' => $model->username,
            'ip' => $model->ip,
            'os' => $model->os,
            'browser' => $model->browser,
            'status' => $model->status,
            'message' => $model->message,
            'login_time' => [$model->login_time->startOfDay(), $model->login_time->endOfDay()],
            'remark' => $model->remark,
        ];
    }
}
