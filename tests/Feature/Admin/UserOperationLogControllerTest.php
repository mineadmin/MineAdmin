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
        self::assertFalse($this->hasPermissions('log:userOperation:list'));
        self::assertTrue($this->addPermissions('log:userOperation:list'));
        self::assertTrue($this->hasPermissions('log:userOperation:list'));
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->deletePermissions('log:userOperation:list');
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
        ]);
        $uri = '/admin/user-operation-log';
        $result = $this->delete($uri);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        self::assertFalse($this->hasPermissions('log:userOperation:delete'));
        self::assertTrue($this->addPermissions('log:userOperation:delete'));
        self::assertTrue($this->hasPermissions('log:userOperation:delete'));
        $result = $this->delete($uri, ['token' => $this->token, 'ids' => [$entity->id]]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        try {
            $entity->refresh();
        } catch (\Exception $exception) {
            self::assertInstanceOf(ModelNotFoundException::class, $exception);
        }
        $this->deletePermissions('log:userOperation:delete');
        $result = $this->delete($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }
}
