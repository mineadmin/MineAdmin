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

use App\Model\Enums\User\Status;
use App\Model\Enums\User\Type;
use App\Model\Permission\User;
use App\Repository\Permission\UserRepository;
use Carbon\Carbon;
use Faker\Provider\Internet;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class UserRepositoryTest extends AbstractTestRepository
{
    protected string $repositoryClass = UserRepository::class;

    protected function getAttributes(): array
    {
        /*
         * @property int $id 用户ID，主键
         * @property string $username 用户名
         * @property Type $user_type 用户类型：(100系统用户)
         * @property string $nickname 用户昵称
         * @property string $phone 手机
         * @property string $email 用户邮箱
         * @property string $avatar 用户头像
         * @property string $signed 个人签名
         * @property Status $status 状态 (1正常 2停用)
         * @property string $login_ip 最后登陆IP
         * @property string $login_time 最后登陆时间
         * @property array $backend_setting 后台设置数据
         * @property int $created_by 创建者
         * @property int $updated_by 更新者
         * @property Carbon $created_at 创建时间
         * @property Carbon $updated_at 更新时间
         * @property string $remark 备注
         * @property mixed $password 密码
         */
        return [
            'username' => Str::random(),
            'user_type' => Type::SYSTEM,
            'nickname' => Str::random(),
            'phone' => '13800138000',
            'email' => Str::random() . '@gmail.com',
            'avatar' => 'https://www.hyperf.io/favicon.png',
            'signed' => Str::random(),
            'status' => Status::Normal,
            'login_ip' => Internet::localIpv4(),
            'login_time' => Carbon::now(),
            'backend_setting' => ['key' => 'value'],
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'remark' => Str::random(),
        ];
    }

    /**
     * @param User $model
     */
    protected function getSearchAttributes(Model $model, Collection $entityList): array
    {
        return [
            'unique_username' => $model->username,
            'username' => $model->username,
            'phone' => $model->phone,
            'email' => $model->email,
            'status' => $model->status,
            'user_type' => $model->user_type,
            'nickname' => $model->nickname,
            'created_at' => [$model->created_at->toDateString(), $model->created_at->toDateString()],
            'user_ids' => $entityList->pluck('id')->toArray(),
        ];
    }
}
