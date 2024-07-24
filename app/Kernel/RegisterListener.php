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

namespace App\Kernel;

use Hyperf\Database\Commands\Migrations\GenMigrateCommand;
use Hyperf\Database\Commands\Seeders\GenSeederCommand;
use Hyperf\Database\Migrations\Migrator;
use Hyperf\Database\Seeders\Seed;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

#[Listener]
class RegisterListener implements ListenerInterface
{
    public function __construct(
        private readonly Migrator $migrator,
        private readonly Seed $seed
    ) {}

    public function listen(): array
    {
        return [
            BootApplication::class,
            ConsoleTerminateEvent::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof BootApplication) {
            $this->migrator->path(BASE_PATH . '/databases/migrations');
            $this->seed->path(BASE_PATH . '/databases/seeders');
        }
        if ($event instanceof ConsoleTerminateEvent) {
            $command = $event->getCommand();
            if ($command instanceof GenMigrateCommand) {
                FileSystem::copy(BASE_PATH . '/migrations', BASE_PATH . '/databases/migrations');
            }
            if ($command instanceof GenSeederCommand) {
                FileSystem::copy(BASE_PATH . '/seeders', BASE_PATH . '/databases/seeders');
            }
        }
    }
}
