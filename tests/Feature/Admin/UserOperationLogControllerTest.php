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

namespace HyperfTests\Feature\Admin;

use App\Http\Common\ResultCode;
use App\Model\UserOperationLog;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class UserOperationLogControllerTest extends ControllerCase
{
    public function testPage(): void
    {
        $uri = '/admin/user-operation-log/list';
        $result = $this->get($uri);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        self::assertFalse($enforce->hasPermissionForUser($this->user->username, 'user-operation-log:list'));
        self::assertTrue($enforce->addPermissionForUser($this->user->username, 'user-operation-log:list'));
        self::assertTrue($enforce->hasPermissionForUser($this->user->username, 'user-operation-log:list'));
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertTrue($enforce->deletePermissionForUser($this->user->username, 'user-operation-log:list'));
        self::assertFalse($enforce->hasPermissionForUser($this->user->username, 'user-operation-log:list'));
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }

    public function testDelete(): void
    {
        /**
         * @property string $username 用户名
         * @property string $method 请求方式
         * @property string $router 请求路由
         * @property string $service_name 业务名称
         * @property string $ip 请求IP地址
         * @property string $ip_location IP所属地
         * @property string $request_data 请求数据
         * @property string $response_code 响应状态码
         * @property string $response_data 响应数据
         * @property int $created_by 创建者
         * @property int $updated_by 更新者
         * @property Carbon $created_at 创建时间
         * @property Carbon $updated_at 更新时间
         * @property Carbon $deleted_at 删除时间
         * @property string $remark 备注
         */
        $entity = UserOperationLog::create([
            'username' => Str::random(10),
            'method' => 'GET',
            'router' => '/admin/user-operation-log/list',
            'service_name' => 'UserOperationLogList',
            'ip' => '0.0.0.0',
            'ip_location' => 'localhost',
            'request_data' => '[]',
            'response_code' => '200',
            'response_data' => '[]',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        $uri = '/admin/user-operation-log';
        $result = $this->delete($uri);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        self::assertFalse($enforce->hasPermissionForUser($this->user->username, 'user-operation-log:delete'));
        self::assertTrue($enforce->addPermissionForUser($this->user->username, 'user-operation-log:delete'));
        self::assertTrue($enforce->hasPermissionForUser($this->user->username, 'user-operation-log:delete'));
        $result = $this->delete($uri, ['token' => $this->token, 'ids' => [$entity->id]]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        try {
            $entity->refresh();
        } catch (\Exception $exception) {
            self::assertInstanceOf(ModelNotFoundException::class, $exception);
        }
        self::assertTrue($enforce->deletePermissionForUser($this->user->username, 'user-operation-log:delete'));
        self::assertFalse($enforce->hasPermissionForUser($this->user->username, 'user-operation-log:delete'));
        $result = $this->delete($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }
}
