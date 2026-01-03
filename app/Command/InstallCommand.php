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

namespace App\Command;

use Hyperf\Command\Annotation\AsCommand;
use Hyperf\Command\Concerns\InteractsWithIO;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class InstallCommand
{
    use InteractsWithIO;

    #[AsCommand(
        signature: 'mine:install'
    )]
    public function handle(): void
    {
        /** @var \Psr\Container\ContainerInterface $container */
        $container = \Hyperf\Context\ApplicationContext::getContainer();

        /** @var \Symfony\Component\Console\Application $application */
        $application = $container->get(\Hyperf\Contract\ApplicationInterface::class);
        $application->setAutoExit(false);

        $this->output->title('Installing database');

        $application->run(new ArrayInput(['command' => 'migrate', '--force' => true]), $this->output);
        $application->run(new ArrayInput(['command' => 'db:seed']), $this->output);

        $this->output->title('Installing plugins');

        foreach (config('mine-install.allow_auto_install_plugins') as $plugin) {
            $application->run(
                new ArrayInput(['command' => 'mine-extension:install', 'path' => $plugin, '--yes' => true]),
                $this->output
            );
        }

        $this->output->success('Installation completed');
    }
}
