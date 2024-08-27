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

use Hyperf\Context\ApplicationContext;
use Mine\Kernel\Casbin\Factory;
use Mine\Kernel\Casbin\Rule\Rule;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ApiTest extends TestCase
{
    protected function setUp(): void
    {
        Rule::whereRaw('1=1')->delete();
        $this->getEnforce()->enableLog();
    }

    protected function tearDown(): void
    {
        $this->getEnforce()->enableLog(false);
    }

    public function testAddPolicy()
    {
        $enforce = $this->getEnforce();
        $this->assertTrue($enforce->addRoleForUser('alice', 'admin'));
        $this->assertTrue($enforce->addRoleForUser('admin', 'data1'));
        $this->assertTrue($enforce->hasRoleForUser('alice', 'admin'));
        $this->assertTrue($enforce->hasRoleForUser('admin', 'data1'));
        $this->assertFalse($enforce->hasRoleForUser('alice', 'data1'));
        $this->assertSame(['admin'], $enforce->getRoleManager()->getRoles('alice'));
        $this->assertSame(['data1'], $enforce->getRoleManager()->getRoles('admin'));
        $this->assertSame(['alice'], $enforce->getRoleManager()->getUsers('admin'));
        $this->assertSame(['admin'], $enforce->getRoleManager()->getUsers('data1'));

        // add multiple policies
        $this->assertTrue($enforce->addPolicies([
            ['alice', 'data2', 'read'],
            ['bob', 'data1', 'write'],
        ]));
        $this->assertTrue($enforce->hasPolicy('alice', 'data2', 'read'));
        $this->assertTrue($enforce->hasPolicy('bob', 'data1', 'write'));
        $this->assertFalse($enforce->hasPolicy('alice', 'data1', 'read'));
        $this->assertFalse($enforce->hasPolicy('bob', 'data2', 'write'));

        // add policy
        $this->assertTrue($enforce->addPolicy('alice', 'data3', 'read'));
        $this->assertTrue($enforce->hasPolicy('alice', 'data3', 'read'));
        $this->assertFalse($enforce->hasPolicy('alice', 'data3', 'write'));
        $this->assertTrue($enforce->addPolicy('alice', 'data3', 'write'));

        // clear
        Rule::whereRaw('1=1')->delete();
        // add multiple role & multiple user
        $user = 'test1';
        for ($i = 0; $i < 10; ++$i) {
            $role = 'role' . $i;
            $enforce->addRoleForUser($user, $role);
        }

        $this->assertSame(10, count($enforce->getRoleManager()->getRoles($user)));
        $this->assertSame(1, count($enforce->getRoleManager()->getUsers('role1')));
        $this->assertSame(1, count($enforce->getRoleManager()->getUsers('role2')));
        $this->assertSame(['test1'], $enforce->getRoleManager()->getUsers('role1'));
        $this->assertSame(['test1'], $enforce->getRoleManager()->getUsers('role2'));
    }

    private function getEnforce()
    {
        return ApplicationContext::getContainer()->get(Factory::class)->enforcer();
    }
}
