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

use App\Model\Permission\Menu;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\CrudControllerCase;

/**
 * @internal
 * @coversNothing
 */
class MenuControllerTest extends CrudControllerCase
{
    public function testPageList(): void
    {
        $this->casePageList('/admin/menu/list', 'menu:list');
    }

    public function testCreate(): void
    {
        $this->caseCreate('/admin/menu', 'menu:create', [
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
        ], Menu::class);
    }

    public function testSave(): void
    {
        $entity = Menu::create([
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
        ]);
        $this->caseSave('/admin/menu/', $entity, 'menu:save', [
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
        ]);
    }

    public function testDelete(): void
    {
        $entity = Menu::create([
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
        ]);
        $this->caseDelete('/admin/menu', $entity, 'menu:delete');
    }
}
