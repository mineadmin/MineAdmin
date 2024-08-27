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

use Casbin\Enforcer;
use Hyperf\Context\ApplicationContext;
use Mine\Kernel\Casbin\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FactoryTest extends TestCase
{
    public function testFactory()
    {
        $factory = ApplicationContext::getContainer()->get(Factory::class);
        $this->assertInstanceOf(Enforcer::class, $factory->enforcer());
    }
}
