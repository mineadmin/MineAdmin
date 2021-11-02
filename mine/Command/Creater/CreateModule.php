<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Command\Creater;

use Hyperf\Command\Annotation\Command;
use Mine\Mine;
use Mine\MineCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreateModule
 * @Command
 * @package System\Command\Creater
 */
class CreateModule extends MineCommand
{
    protected $name = 'mine:module-gen';

    protected function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:module-gen <--name | -n <module>>"');
        $this->setDescription('Create new module of MineAdmin system');
    }

    public function handle()
    {
        $mine = make(Mine::class);
        $name = ucfirst(trim($this->input->getOption('name')));
    }

    protected function getOptions(): array
    {
        return [
            ['name', '-n', InputOption::VALUE_REQUIRED, 'Please enter the module name']
        ];
    }
}