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
use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use Carbon\Carbon;
use Hyperf\Codec\Json;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\Controller;
use HyperfTests\Feature\Admin\GetTokenTrait;

/**
 * @internal
 * @coversNothing
 */
class MenuController extends Controller
{
    use GetTokenTrait;

    public function testInfo()
    {
        $token = $this->token;
        $noTokenResult = $this->get('/admin/permission/menu/list');
        $this->assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);

        $result = $this->get('/admin/permission/menu/list', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);

        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertCount(0, Arr::get($result, 'data'));

        /**
         * @var Model[] $deleteModels
         */
        $deleteModels = [];

        /**
         * @var Role $role
         */
        $role = $this->user->roles()->create([
            'name' => 'test',
            'code' => 'test',
            'data_scope' => 1,
            'status' => 1,
            'sort' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
            'remark' => 'test',
        ]);
        $deleteModels[] = $role;
        $menuList = Collection::make();
        for ($i = 0; $i <= 2; ++$i) {
            /**
             * @var Menu $menu
             */
            $menu = $role->menus()->create([
                'parent_id' => 0,
                'name' => Str::random(10),
                'code' => Str::random(10),
                'icon' => 'test',
                'route' => 'test',
                'component' => 'test',
                'redirect' => 'test',
                'is_hidden' => 1,
                'type' => 'M',
                'status' => 1,
                'sort' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
                'remark' => 'test',
            ]);
            $deleteModels[] = $role->menus()->create([
                'parent_id' => $menu->id,
                'name' => Str::random(10),
                'code' => Str::random(10),
                'icon' => 'test',
                'route' => 'test',
                'component' => 'test',
                'redirect' => 'test',
                'is_hidden' => 1,
                'type' => 'M',
                'status' => 1,
                'sort' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
                'remark' => 'test',
            ]);
            $menuList[] = $menu;
            $deleteModels[] = $menu;
        }

        $result = $this->get('/admin/permission/menu/list', [], ['Authorization' => 'Bearer ' . $token]);

        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertCount($menuList->count() * 2, Arr::get($result, 'data'));

        var_dump(Json::encode($result));

        foreach (Arr::get($result, 'data') as $menu) {
            $this->assertArrayHasKey('id', $menu);
            $this->assertArrayHasKey('parent_id', $menu);
            $this->assertArrayHasKey('name', $menu);
            $this->assertArrayHasKey('code', $menu);
            $this->assertArrayHasKey('icon', $menu);
            $this->assertArrayHasKey('route', $menu);
            $this->assertArrayHasKey('component', $menu);
            $this->assertArrayHasKey('redirect', $menu);
            $this->assertArrayHasKey('is_hidden', $menu);
            $this->assertArrayHasKey('type', $menu);
            $this->assertArrayHasKey('status', $menu);
            $this->assertArrayHasKey('sort', $menu);
            $this->assertArrayHasKey('created_by', $menu);
            $this->assertArrayHasKey('updated_by', $menu);
            $this->assertArrayHasKey('created_at', $menu);
            $this->assertArrayHasKey('updated_at', $menu);
            $this->assertArrayHasKey('deleted_at', $menu);
            $this->assertArrayHasKey('remark', $menu);
        }

        foreach ($deleteModels as $model) {
            $model->forceDelete();
        }
    }
}
