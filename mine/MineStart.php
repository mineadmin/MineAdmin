<?php
declare(strict_types=1);
namespace Mine;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Framework\Bootstrap\ServerStartCallback;
use Hyperf\Utils\ApplicationContext;
use Swoole\Timer;

class MineStart extends ServerStartCallback
{
    public function beforeStart()
    {
        $console = console();
        $console->info('MineAdmin start success...');
        $console->info($this->welcome());
    }

    protected function welcome(): string
    {
        return sprintf('
/---------------------- welcome to use -----------------------\
|               _                ___       __          _      |
|    ____ ___  (_)___  _____    /   | ____/ /___ ___  (_)___  |
|   / __ `__ \/ / __ \/ ___/   / /| |/ __  / __ `__ \/ / __ \ |
|  / / / / / / / / / / /__/   / ___ / /_/ / / / / / / / / / / |
| /_/ /_/ /_/_/_/ /_/\___/   /_/  |_\__,_/_/ /_/ /_/_/_/ /_/  |
|                                                             |
\_____________  Copyright MineAdmin 2021 ~ %s  _____________|
', date('Y'));
    }
}