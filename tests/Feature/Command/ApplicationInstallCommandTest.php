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

namespace HyperfTests\Feature\Command;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @internal
 * @coversNothing
 */
#[Group('migrations')]
final class ApplicationInstallCommandTest extends TestCase
{
    public function testRun(): void
    {
        $app = ApplicationContext::getContainer()->get(ApplicationInterface::class);
        $app->setAutoExit(false);
        $app->run(new ArrayInput(['migrate']), new ConsoleOutput());
        $app->run(new ArrayInput(['db:seed']), new ConsoleOutput());
        self::assertTrue(true);
    }
}
