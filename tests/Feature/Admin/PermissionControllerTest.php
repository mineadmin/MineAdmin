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
use Hyperf\Stringable\Str;
use Mine\Casbin\Rule\Rule;

/**
 * @internal
 * @coversNothing
 */
final class PermissionControllerTest extends ControllerCase
{
    public function testMenus(): void
    {
        User::truncate();
        Rule::truncate();
        Menu::truncate();
        $user = $this->generatorUser();
        $token = $this->getToken($user);
        $noTokenResult = $this->get('/admin/permission/menus');
        self::assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $menus = [
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
                'status' => Status::ENABLE,
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
                'status' => Status::ENABLE,
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
                'status' => Status::ENABLE,
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
                'status' => Status::ENABLE,
                'sort' => rand(1, 100),
                'remark' => Str::random(10),
            ]),
        ];
        $role = Role::create([
            'name' => Str::random(10),
            'code' => Str::random(10),
            'sort' => rand(1, 100),
            'status' => Status::ENABLE,
            'remark' => Str::random(),
        ]);
        $superAdminRole = Role::create([
            'name' => Str::random(10),
            'code' => 'SuperAdmin',
            'sort' => rand(1, 100),
            'status' => Status::ENABLE,
            'remark' => Str::random(),
        ]);
        $menuIds = [];
        Arr::map($menus, static function (Menu $menu) use (&$menuIds) {
            if (\count($menuIds) > 2) {
                return;
            }
            $menuIds[$menu->name] = [
                'ptype' => 'p',
            ];
        });
        $role->menus()->sync($menuIds);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);
        $user->roles()->sync([
            $superAdminRole->code => [
                'ptype' => 'g',
            ],
        ]);

        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), \count($menus));
        $user->roles()->detach();
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);

        $user->roles()->sync([
            $role->code => [
                'ptype' => 'g',
            ],
        ]);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 3);
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
        Rule::truncate();
        Role::truncate();
        $user = $this->generatorUser();
        $token = $this->getToken($user);
        $role = [
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => Status::ENABLE,
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => 'SuperAdmin',
                'sort' => rand(1, 100),
                'status' => Status::ENABLE,
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => Status::ENABLE,
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => Status::ENABLE,
                'remark' => Str::random(),
            ]),
        ];
        $noTokenResult = $this->get('/admin/permission/roles');
        self::assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        User::query()->where('username', 'SuperAdmin')->forceDelete();
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertSame(\count(Arr::get($result, 'data')), 0);
        $role = Role::where('code', 'SuperAdmin')->value('code');
        $user->roles()->sync([
            $role => [
                'ptype' => 'g',
            ],
        ]);
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
            'password' => Str::random(10),
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
    }
}
