<?php
declare(strict_types=1);
namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Seeders\Seed;
use Hyperf\DbConnection\Db;
use Mine\Helper\Id;
use Mine\MineCommand;
use Mine\Mine;

/**
 * Class UpdateProjectCommand
 * @Command
 * @package System\Command
 */
class UpdateProjectCommand extends MineCommand
{
    /**
     * 更新项目命令
     * @var string
     */
    protected $name = 'mine:update';

    protected $database = [];

    protected $redis = [];

    protected $seed;

    public function __construct(string $name = null, Seed $seed)
    {
        parent::__construct($name);
        $this->seed = $seed;
    }

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:update" Update MineAdmin system');
        $this->setDescription('MineAdmin system update command');
    }

    public function handle()
    {
        $modules = make(Mine::class)->getModuleInfo();
        $basePath = BASE_PATH . '/app/';
        foreach ($modules as $name => $module) {
            $path = $basePath . $name . '/Database/Seeders/Update';

            if (is_dir($path)) {
                $this->seed->run([$path]);
            }
        }

        $this->line($this->getGreenText('updated successfully...'));
    }


}