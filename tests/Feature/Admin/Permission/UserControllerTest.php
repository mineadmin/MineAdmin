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

namespace HyperfTests\Feature\Admin\Permission;

use App\Http\Common\ResultCode;
use App\Model\Permission\User;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\Controller;

/**
 * @internal
 * @coversNothing
 */
class UserControllerTest extends Controller
{
    public function testList(): void
    {
        $token = $this->token;

        $noTokenResult = $this->get('/admin/user/list');
        $this->assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);

        $result = $this->get('/admin/user/list', ['token' => $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, 'user:list'));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, 'user:list'));
        $this->assertTrue($enforce->hasPermissionForUser($this->user->username, 'user:list'));
        $result = $this->get('/admin/user/list', ['token' => $token]);

        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertSame(Arr::get($result, 'data.total'), User::query()->count());

        $this->assertTrue($enforce->deletePermissionForUser($this->user->username, 'user:list'));

        $result = $this->get('/admin/user/list', ['token' => $token]);

        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }

    public function testCreate(): void
    {
        $token = $this->token;
        $attributes = [
            'username',
            'user_type',
            'nickname',
        ];
        foreach ($attributes as $attribute) {
            $result = $this->post('/admin/user', [$attribute => '']);
            $this->assertSame(Arr::get($result, 'code'), ResultCode::UNPROCESSABLE_ENTITY->value);
        }
        $fillAttributes = [
            'username' => Str::random(),
            'user_type' => 100,
            'nickname' => Str::random(),
        ];
        $result = $this->post('/admin/user', $fillAttributes);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->post('/admin/user', $fillAttributes, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, 'user:create'));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, 'user:create'));
        $this->assertTrue($enforce->hasPermissionForUser($this->user->username, 'user:create'));
        $result = $this->post('/admin/user', $fillAttributes, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsString($this->getToken(User::query()->where('username', $fillAttributes['username'])->first()));
        User::query()->where('username', $fillAttributes['username'])->forceDelete();
        $fillAttributes = [
            'username' => Str::random(),
            'user_type' => 100,
            'nickname' => Str::random(),
            'phone' => Str::random(8),
            'email' => Str::random(10) . '@qq.com',
            'avatar' => 'https://www.baidu.com',
            'signed' => 'test',
            'dashboard' => 'test',
            'status' => 1,
            'backend_setting' => 'test',
            'remark' => 'test',
        ];
        $result = $this->post('/admin/user', $fillAttributes, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsString($this->getToken(User::query()->where('username', $fillAttributes['username'])->first()));
        User::query()->where('username', $fillAttributes['username'])->forceDelete();
    }

    public function testDelete(): void
    {
        $user = User::create([
            'username' => Str::random(),
            'user_type' => 100,
            'nickname' => Str::random(),
        ]);
        $token = $this->token;
        $result = $this->delete('/admin/user/' . $user->id);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete('/admin/user/' . $user->id, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, 'user:delete'));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, 'user:delete'));
        $this->assertTrue($enforce->hasPermissionForUser($this->user->username, 'user:delete'));
        $result = $this->delete('/admin/user/' . $user->id, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $user->refresh();
        $this->assertTrue($user->trashed());
        $user->forceDelete();
    }

    public function testSave(): void
    {
        $user = User::create([
            'username' => Str::random(),
            'user_type' => 100,
            'nickname' => Str::random(),
        ]);
        $token = $this->token;
        $result = $this->put('/admin/user/' . $user->id);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->put('/admin/user/' . $user->id, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, 'user:save'));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, 'user:save'));
        $this->assertTrue($enforce->hasPermissionForUser($this->user->username, 'user:save'));
        $result = $this->put('/admin/user/' . $user->id, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $fillAttributes = [
            'username' => Str::random(),
            'user_type' => 100,
            'nickname' => Str::random(),
            'phone' => Str::random(8),
            'email' => Str::random(10) . '@qq.com',
            'avatar' => 'https://www.baidu.com',
            'signed' => 'test',
            'dashboard' => 'test',
            'status' => 1,
            'backend_setting' => ['testxx'],
            'remark' => 'test',
        ];
        $result = $this->put('/admin/user/' . $user->id, $fillAttributes, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $user->refresh();
        $this->assertSame($user->username, $fillAttributes['username']);
        $this->assertSame($user->user_type, (int) $fillAttributes['user_type']);
        $this->assertSame($user->nickname, $fillAttributes['nickname']);
        $this->assertSame($user->phone, $fillAttributes['phone']);
        $this->assertSame($user->email, $fillAttributes['email']);
        $this->assertSame($user->avatar, $fillAttributes['avatar']);
        $this->assertSame($user->signed, $fillAttributes['signed']);
        $this->assertSame($user->dashboard, $fillAttributes['dashboard']);
        $this->assertSame($user->status, $fillAttributes['status']);
        $this->assertSame($user->backend_setting, $fillAttributes['backend_setting']);
        $this->assertSame($user->remark, $fillAttributes['remark']);
        $user->forceDelete();
    }
}
