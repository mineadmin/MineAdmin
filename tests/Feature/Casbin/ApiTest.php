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
use Mine\Casbin\Factory;
use Mine\Casbin\Rule\Rule;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ApiTest extends TestCase
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
        self::assertTrue($enforce->addRoleForUser('alice', 'admin'));
        self::assertTrue($enforce->addRoleForUser('admin', 'data1'));
        self::assertTrue($enforce->hasRoleForUser('alice', 'admin'));
        self::assertTrue($enforce->hasRoleForUser('admin', 'data1'));
        self::assertFalse($enforce->hasRoleForUser('alice', 'data1'));
        self::assertSame(['admin'], $enforce->getRoleManager()->getRoles('alice'));
        self::assertSame(['data1'], $enforce->getRoleManager()->getRoles('admin'));
        self::assertSame(['alice'], $enforce->getRoleManager()->getUsers('admin'));
        self::assertSame(['admin'], $enforce->getRoleManager()->getUsers('data1'));

        // add multiple policies
        self::assertTrue($enforce->addPolicies([
            ['alice', 'data2', 'read'],
            ['bob', 'data1', 'write'],
        ]));
        self::assertTrue($enforce->hasPolicy('alice', 'data2', 'read'));
        self::assertTrue($enforce->hasPolicy('bob', 'data1', 'write'));
        self::assertFalse($enforce->hasPolicy('alice', 'data1', 'read'));
        self::assertFalse($enforce->hasPolicy('bob', 'data2', 'write'));

        // add policy
        self::assertTrue($enforce->addPolicy('alice', 'data3', 'read'));
        self::assertTrue($enforce->hasPolicy('alice', 'data3', 'read'));
        self::assertFalse($enforce->hasPolicy('alice', 'data3', 'write'));
        self::assertTrue($enforce->addPolicy('alice', 'data3', 'write'));

        // clear
        Rule::whereRaw('1=1')->delete();
        // add multiple role & multiple user
        $user = 'test1';
        for ($i = 0; $i < 10; ++$i) {
            $role = 'role' . $i;
            $enforce->addRoleForUser($user, $role);
        }

        self::assertSame(10, \count($enforce->getRoleManager()->getRoles($user)));
        self::assertSame(1, \count($enforce->getRoleManager()->getUsers('role1')));
        self::assertSame(1, \count($enforce->getRoleManager()->getUsers('role2')));
        self::assertSame(['test1'], $enforce->getRoleManager()->getUsers('role1'));
        self::assertSame(['test1'], $enforce->getRoleManager()->getUsers('role2'));
    }

    private function getEnforce()
    {
        return ApplicationContext::getContainer()->get(Factory::class)->enforcer();
    }
}
