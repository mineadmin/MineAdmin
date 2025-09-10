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
use App\Model\Enums\User\Status;
use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use App\Model\Permission\User;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Stringable\Str;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @coversNothing
 */
final class PermissionControllerTest extends ControllerCase
{
    protected string $password = '123456';

    public function testMenus(): void
    {
        Menu::truncate();
        $user = $this->generatorUser();
        $token = $this->getToken($user);
        $noTokenResult = $this->get('/admin/permission/menus');
        self::assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertCount(0, Arr::get($result, 'data'));
        $menus = Collection::make([
            Menu::create([
                'parent_id' => 0,
                'name' => Str::random(10),
                'code' => Str::random(10),
                'icon' => Str::random(10),
                'route' => Str::random(10),
                'component' => Str::random(10),
                'redirect' => Str::random(10),
                'is_hidden' => rand(0, 1),
                'type' => Str::random(1),
                'status' => Status::Normal,
                'sort' => rand(1, 100),
                'remark' => Str::random(10),
            ]),
            Menu::create([
                'parent_id' => 0,
                'name' => Str::random(10),
                'code' => Str::random(10),
                'icon' => Str::random(10),
                'route' => Str::random(10),
                'component' => Str::random(10),
                'redirect' => Str::random(10),
                'is_hidden' => rand(0, 1),
                'type' => Str::random(1),
                'status' => Status::Normal,
                'sort' => rand(1, 100),
                'remark' => Str::random(10),
            ]),
            Menu::create([
                'parent_id' => 0,
                'name' => Str::random(10),
                'code' => Str::random(10),
                'icon' => Str::random(10),
                'route' => Str::random(10),
                'component' => Str::random(10),
                'redirect' => Str::random(10),
                'is_hidden' => rand(0, 1),
                'type' => Str::random(1),
                'status' => Status::Normal,
                'sort' => rand(1, 100),
                'remark' => Str::random(10),
            ]),
            Menu::create([
                'parent_id' => 0,
                'name' => Str::random(10),
                'code' => Str::random(10),
                'icon' => Str::random(10),
                'route' => Str::random(10),
                'component' => Str::random(10),
                'redirect' => Str::random(10),
                'is_hidden' => rand(0, 1),
                'type' => Str::random(1),
                'status' => Status::Normal,
                'sort' => rand(1, 100),
                'remark' => Str::random(10),
            ]),
        ]);
        $role = Role::create([
            'name' => Str::random(10),
            'code' => Str::random(10),
            'sort' => rand(1, 100),
            'status' => Status::Normal,
            'remark' => Str::random(),
        ]);
        Role::where('code', 'SuperAdmin')->forceDelete();
        $superAdminRole = Role::create([
            'name' => Str::random(10),
            'code' => 'SuperAdmin',
            'sort' => rand(1, 100),
            'status' => Status::Normal,
            'remark' => Str::random(),
        ]);

        $role->menus()->sync($menus->pluck('id')->toArray());

        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);
        $user->roles()->sync($superAdminRole);

        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), \count($menus));
        $user->roles()->detach();
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);

        $user->roles()->sync($role);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 4);
        $role->menus()->detach();
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);
        $role->forceDelete();
    }

    public function testRoles(): void
    {
        User::truncate();
        Role::truncate();
        $user = $this->generatorUser();
        $token = $this->getToken($user);
        $roles = [
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => Status::Normal,
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => 'SuperAdmin',
                'sort' => rand(1, 100),
                'status' => Status::Normal,
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => Status::Normal,
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => Status::Normal,
                'remark' => Str::random(),
            ]),
        ];
        $noTokenResult = $this->get('/admin/permission/roles');
        self::assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        User::query()->where('username', 'SuperAdmin')->forceDelete();
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        $role = Role::where('code', 'SuperAdmin')->first();
        $user->roles()->sync($role);
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 4);

        $user->roles()->detach();
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);
    }

    public function testUpdateInfo()
    {
        $user = $this->generatorUser();
        $token = $this->getToken($user);
        $noTokenResult = $this->post('/admin/permission/update');
        self::assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $payload = [
            'nickname' => Str::random(10),
            'avatar' => Str::random(10),
            'signed' => Str::random(10),
            'backend_setting' => [
                Str::random(10),
            ],
        ];
        $result = $this->post('/admin/permission/update', $payload, ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $user->refresh();
        self::assertSame($user->nickname, Arr::get($payload, 'nickname'));
        self::assertSame($user->avatar, Arr::get($payload, 'avatar'));
        self::assertSame($user->signed, Arr::get($payload, 'signed'));
        self::assertSame($user->backend_setting, Arr::get($payload, 'backend_setting'));

        // test update password
        $oldPassword = Str::random(10);
        $user->password = $oldPassword;
        $user->save();
        $user->refresh();
        ApplicationContext::getContainer()->get(CacheInterface::class)
            ->delete((string) $user->id);
        $this->password = $oldPassword;
        $newPassword = Str::random(10);
        $payload = [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
        ];
        $token = $this->getToken($user);
        $result = $this->post('/admin/permission/update', $payload, ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $user->refresh();
        self::assertTrue($user->verifyPassword($newPassword));
    }
}
