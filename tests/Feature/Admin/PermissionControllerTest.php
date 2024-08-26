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
use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use App\Model\Permission\User;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use Mine\Kernel\Casbin\Rule\Rule;

/**
 * @internal
 * @coversNothing
 */
class PermissionControllerTest extends ControllerCase
{
    public function testMenus(): void
    {
        User::truncate();
        Rule::truncate();
        Menu::truncate();
        $user = $this->generatorUser();
        $token = $this->getToken($user);
        $noTokenResult = $this->get('/admin/permission/menus');
        $this->assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
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
                'status' => rand(0, 1),
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
                'status' => rand(0, 1),
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
                'status' => rand(0, 1),
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
                'status' => rand(0, 1),
                'sort' => rand(1, 100),
                'remark' => Str::random(10),
            ]),
        ];
        $role = Role::create([
            'name' => Str::random(10),
            'code' => Str::random(10),
            'sort' => rand(1, 100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $menuIds = [];
        Arr::map($menus, function (Menu $menu) use (&$menuIds) {
            $menuIds[$menu->id] = [
                'ptype' => 'p',
            ];
        });
        $role->menus()->sync($menuIds);
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertSame(count(Arr::get($result, 'data')), 0);

        User::query()->where('username', 'SuperAdmin')->forceDelete();
        $user->fill(['username' => 'SuperAdmin'])->save();

        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertSame(count(Arr::get($result, 'data')), count($menus));

        $user->fill(['username' => Str::random()])->save();
        $result = $this->get('/admin/permission/menus', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertSame(count(Arr::get($result, 'data')), 0);

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
                'status' => rand(0, 1),
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => rand(0, 1),
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => rand(0, 1),
                'remark' => Str::random(),
            ]),
            Role::create([
                'name' => Str::random(10),
                'code' => Str::random(10),
                'sort' => rand(1, 100),
                'status' => rand(0, 1),
                'remark' => Str::random(),
            ]),
        ];
        $noTokenResult = $this->get('/admin/permission/roles');
        $this->assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        User::query()->where('username', 'SuperAdmin')->forceDelete();
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertSame(count(Arr::get($result, 'data')), 0);

        $user->fill(['username' => 'SuperAdmin'])->save();
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertSame(count(Arr::get($result, 'data')), count($role));

        $user->fill(['username' => Str::random()])->save();
        $result = $this->get('/admin/permission/roles', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertSame(count(Arr::get($result, 'data')), 0);
    }
}
