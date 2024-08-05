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

use App\Model\Permission\Role;
use App\Model\Permission\User;
use App\Service\PermissionService;
use Hyperf\Context\ApplicationContext;
use Hyperf\Stringable\Str;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UserToManyRoleTest extends TestCase
{
    public function testUserRole(): void
    {
        $user = User::create([
            'username' => Str::random(),
            'user_type' => 100,
            'nickname' => Str::random(),
        ]);
        $roleEntity = Role::create([
            'name' => Str::random(10),
            'code' => Str::random(10),
            'sort' => rand(1, 100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $enforce = $this->getEnforce();
        $this->assertTrue($enforce->addRoleForUser($user->username, $roleEntity->code));
        $this->assertTrue($enforce->hasRoleForUser($user->username, $roleEntity->code));
        $roles = $user->roles()->get();
        $users = $roleEntity->users()->get();
        $this->assertTrue($roles->contains($roleEntity));
        $this->assertTrue($users->contains($user));
        $this->assertTrue($enforce->deleteRoleForUser($user->username, $roleEntity->code));
        $this->assertFalse($enforce->hasRoleForUser($user->username, $roleEntity->code));

        $roles = $user->roles()->get();
        $users = $roleEntity->users()->get();

        $this->assertFalse($users->contains($user));
        $this->assertFalse($roles->contains($roleEntity));

        $user->roles()->detach();
        $user->forceDelete();
        $roleEntity->forceDelete();
    }

    protected function getEnforce()
    {
        return ApplicationContext::getContainer()->get(PermissionService::class)->getEnforce();
    }
}
