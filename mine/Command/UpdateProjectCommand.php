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

namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Seeders\Seed;
use Hyperf\Database\Migrations\Migrator;
use Mine\MineCommand;
use Mine\Mine;

/**
 * Class UpdateProjectCommand
 * @package System\Command
 */
#[Command]
class UpdateProjectCommand extends MineCommand
{
    /**
     * 更新项目命令
     * @var string|null
     */
    protected ?string $name = 'mine:update';

    protected array $database = [];

    protected Seed $seed;

    protected Migrator $migrator;

    /**
     * UpdateProjectCommand constructor.
     * @param string|null $name
     * @param Migrator $migrator
     * @param Seed $seed
     */
    public function __construct(string $name = null, Migrator $migrator, Seed $seed)
    {
        parent::__construct($name);
        $this->migrator = $migrator;
        $this->seed = $seed;
    }

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:update" Update MineAdmin system');
        $this->setDescription('MineAdmin system update command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $modules = make(Mine::class)->getModuleInfo();
        $basePath = BASE_PATH . '/app/';
        $this->migrator->setConnection('default');

        foreach ($modules as $name => $module) {
            $seedPath = $basePath . $name . '/Database/Seeders/Update';
            $migratePath = $basePath . $name . '/Database/Migrations/Update';

            if (is_dir($migratePath)) {
                $this->migrator->run([$migratePath]);
            }

            if (is_dir($seedPath)) {
                $this->seed->run([$seedPath]);
            }
        }

        redis()->flushDB();

        $this->line($this->getGreenText('updated successfully...'));
    }
}
