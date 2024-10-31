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
use App\Model\Enums\User\Type;
use App\Model\Permission\User;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use HyperfTests\HttpTestCase;

/**
 * @internal
 * @coversNothing
 */
final class PassportControllerTest extends HttpTestCase
{
    public function testLoginUsernameNotFound()
    {
        User::where('username', 'admin')->forceDelete();
        $result = $this->post('/admin/passport/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNPROCESSABLE_ENTITY->value);
    }

    public function testLoginUserPasswordFail()
    {
        $user = User::create([
            'username' => Str::random(10),
            'password' => password_hash('123456', \PASSWORD_DEFAULT),
        ]);

        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => 'admin',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNPROCESSABLE_ENTITY->value);
        $user->forceDelete();
    }

    public function testLoginExistUsername()
    {
        $user = User::create([
            'username' => uniqid('admin'),
            'password' => password_hash('admin', \PASSWORD_DEFAULT),
            'user_type' => Type::USER,
        ]);
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => 'admin',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::NOT_FOUND->value);
        $user->forceDelete();
    }

    public function testLoginSuccess()
    {
        $user = User::create([
            'username' => Str::random(10),
            'password' => 123456,
        ]);

        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertArrayHasKey('access_token', $result['data']);
        self::assertArrayHasKey('expire_at', $result['data']);
        self::assertIsInt($result['data']['expire_at']);
        $user->forceDelete();
    }

    public function testLogoutFail()
    {
        $result = $this->post('/admin/passport/logout');
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
    }

    public function testLogoutSuccess()
    {
        $user = User::create([
            'username' => Str::random(10),
            'password' => 123456,
        ]);
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertArrayHasKey('access_token', $result['data']);
        self::assertArrayHasKey('expire_at', $result['data']);
        self::assertIsInt($result['data']['expire_at']);
        $token = $result['data']['access_token'];
        $result = $this->post('/admin/passport/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $result = $this->post('/admin/passport/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $user->forceDelete();
    }

    public function testGetInfo(): void
    {
        $user = User::create([
            'username' => Str::random(10),
            'password' => 123456,
        ]);
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertArrayHasKey('access_token', $result['data']);
        self::assertArrayHasKey('expire_at', $result['data']);
        self::assertIsInt($result['data']['expire_at']);

        $info = $this->get('/admin/passport/getInfo');

        self::assertSame(Arr::get($info, 'code'), ResultCode::UNAUTHORIZED->value);

        $info = $this->get('/admin/passport/getInfo', [], [
            'Authorization' => 'Bearer ' . $result['data']['access_token'],
        ]);

        self::assertSame(Arr::get($info, 'code'), ResultCode::SUCCESS->value);
        $attributes = $user->toArray();
        foreach (Arr::only($attributes, ['username', 'nickname', 'avatar', 'signed', 'backend_setting']) as $key => $value) {
            self::assertSame($value, Arr::get($info, 'data.' . $key));
        }
        $user->forceDelete();
    }

    public function testRefreshToken(): void
    {
        $user = User::create([
            'username' => Str::random(10),
            'password' => 123456,
        ]);
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456',
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertArrayHasKey('access_token', $result['data']);
        self::assertArrayHasKey('expire_at', $result['data']);
        self::assertIsInt($result['data']['expire_at']);

        $info = $this->get('/admin/passport/getInfo');

        self::assertSame(Arr::get($info, 'code'), ResultCode::UNAUTHORIZED->value);
        $orlToken = $result['data']['access_token'];
        $info = $this->get('/admin/passport/getInfo', [], [
            'Authorization' => 'Bearer ' . $orlToken,
        ]);

        self::assertSame(Arr::get($info, 'code'), ResultCode::SUCCESS->value);
        $attributes = $user->toArray();
        foreach (Arr::only($attributes, ['username', 'nickname', 'avatar', 'signed', 'backend_setting']) as $key => $value) {
            self::assertSame($value, Arr::get($info, 'data.' . $key));
        }

        $refresh = $this->post('/admin/passport/refresh', [], [
            'Authorization' => 'Bearer ' . $result['data']['refresh_token'],
        ]);

        self::assertSame(Arr::get($refresh, 'code'), ResultCode::SUCCESS->value);
        self::assertArrayHasKey('access_token', $refresh['data']);
        self::assertArrayHasKey('expire_at', $refresh['data']);

        $info = $this->get('/admin/passport/getInfo', [], [
            'Authorization' => 'Bearer ' . $orlToken,
        ]);
        self::assertSame(Arr::get($info, 'code'), ResultCode::SUCCESS->value);
        $info = $this->get('/admin/passport/getInfo', [], [
            'Authorization' => 'Bearer ' . $refresh['data']['access_token'],
        ]);
        self::assertSame(Arr::get($info, 'code'), ResultCode::SUCCESS->value);
        $user->forceDelete();
    }
}
