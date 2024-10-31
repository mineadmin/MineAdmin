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
use App\Model\Permission\Meta;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\CrudControllerCase;

/**
 * @internal
 * @coversNothing
 */
final class MenuControllerTest extends CrudControllerCase
{
    public function testPageList(): void
    {
        $this->casePageList('/admin/menu/list', 'permission:menu:index');
    }

    public function testCreate(): void
    {
        $this->caseCreate('/admin/menu', 'permission:menu:create', [
            'parent_id' => 0,
            'name' => Str::random(10),
            'component' => Str::random(10),
            'redirect' => Str::random(10),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(10),
            'path' => Str::random(10),
            'meta' => $this->generatorMeta(),
        ], Menu::class, [
            'parent_id', 'name', 'component', 'redirect', 'status', 'sort', 'path',
        ]);
    }

    public function testSave(): void
    {
        $entity = Menu::create([
            'parent_id' => 0,
            'name' => Str::random(10),
            'component' => Str::random(10),
            'redirect' => Str::random(10),
            'is_hidden' => rand(0, 1),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(10),
            'meta' => $this->generatorMeta(),
            'path' => Str::random(10),
        ]);
        $this->caseSave('/admin/menu/', $entity, 'permission:menu:save', [
            'name' => Str::random(10),
            'component' => Str::random(10),
            'redirect' => Str::random(10),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(10),
            'meta' => $this->generatorMeta(),
            'path' => Str::random(10),
        ]);
    }

    public function testDelete(): void
    {
        $entity = Menu::create([
            'parent_id' => 0,
            'name' => Str::random(10),
            'component' => Str::random(10),
            'redirect' => Str::random(10),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(10),
            'meta' => $this->generatorMeta(),
        ]);
        $this->caseDelete('/admin/menu', $entity, 'permission:menu:delete', true);
    }

    protected function generatorMeta()
    {
        return new Meta([
            'title' => Str::random(10),
            'i18n' => Str::random(10),
            'badge' => Str::random(10),
            'icon' => Str::random(10),
            'affix' => rand(0, 1),
            'hidden' => rand(0, 1),
            'type' => Str::random(10),
            'cache' => rand(0, 1),
            'link' => Str::random(10),
        ]);
    }
}
