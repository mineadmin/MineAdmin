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

namespace App\Async\Process;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\ProcessManager;

class DemoProcess extends AbstractProcess
{
    public string $name = 'Demo Process';

    public function handle(): void
    {
        while (ProcessManager::isRunning()) {
            $this->container->get(StdoutLoggerInterface::class)->info('Hello Hyperf Process');
            sleep(1);
        }
    }
}
