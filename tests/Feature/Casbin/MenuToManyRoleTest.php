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

namespace HyperfTests\Feature\Casbin;

use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use App\Service\PermissionService;
use Hyperf\Context\ApplicationContext;
use Hyperf\Stringable\Str;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MenuToManyRoleTest extends TestCase
{
    public function testMenuToManyRole()
    {
        $enforce = $this->getEnforce();
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
        $roleEntity = Role::create([
            'name' => Str::random(10),
            'code' => Str::random(10),
            'sort' => rand(1, 100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $this->assertTrue($enforce->addPermissionForUser($roleEntity->code, $entity->code));
        $this->assertTrue($enforce->hasPermissionForUser($roleEntity->code, $entity->code));
        $menus = $roleEntity->menus()->get();
        $this->assertTrue($menus->contains($entity));

        $roles = $entity->roles()->get();
        $this->assertTrue($roles->contains($roleEntity));
        $this->assertTrue($enforce->deletePermissionForUser($roleEntity->code, $entity->code));
        $this->assertFalse($enforce->hasPermissionForUser($roleEntity->code, $entity->code));

        $menus = $roleEntity->menus()->get();
        $this->assertFalse($menus->contains($entity));
        $roles = $entity->roles()->get();
        $this->assertFalse($roles->contains($roleEntity));

        $roleEntity->menus()->detach();
        $roleEntity->forceDelete();
        $entity->forceDelete();
    }

    protected function getEnforce()
    {
        return ApplicationContext::getContainer()->get(PermissionService::class)->getEnforce();
    }
}
