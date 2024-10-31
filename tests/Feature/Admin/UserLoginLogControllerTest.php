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
use App\Model\UserLoginLog;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class UserLoginLogControllerTest extends ControllerCase
{
    public function testPage(): void
    {
        $uri = '/admin/user-login-log/list';
        $result = $this->get($uri);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        self::assertFalse($this->hasPermissions('log:userLogin:list'));
        self::assertTrue($this->addPermissions('log:userLogin:list'));
        self::assertTrue($this->hasPermissions('log:userLogin:list'));
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->deletePermissions('log:userLogin:list');
        $result = $this->get($uri, ['token' => $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }

    public function testDelete(): void
    {
        /**
         * @property string $username 用户名
         * @property string $ip 登录IP地址
         * @property string $ip_location IP所属地
         * @property string $os 操作系统
         * @property string $browser 浏览器
         * @property int $status 登录状态 (1成功 2失败)
         * @property string $message 提示消息
         * @property string $login_time 登录时间
         * @property string $remark 备注
         */
        $entity = UserLoginLog::create([
            'username' => Str::random(10),
            'ip' => '0.0.0.0',
            'ip_location' => 'localhost',
            'os' => 'Windows',
            'browser' => 'Chrome',
            'status' => 1,
            'message' => 'success',
            'login_time' => Carbon::now(),
        ]);
        $uri = '/admin/user-login-log';
        $result = $this->delete($uri);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($uri, [], ['Authorization' => 'Bearer ' . $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        self::assertFalse($this->hasPermissions('log:userLogin:delete'));
        self::assertTrue($this->addPermissions('log:userLogin:delete'));
        self::assertTrue($this->hasPermissions('log:userLogin:delete'));

        $result = $this->delete($uri, [
            'ids' => [$entity->id],
        ], ['Authorization' => 'Bearer ' . $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->deletePermissions('log:userLogin:delete');
        $result = $this->delete($uri, [], ['Authorization' => 'Bearer ' . $this->token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $this->expectException(ModelNotFoundException::class);
        $entity->refresh();
    }
}
