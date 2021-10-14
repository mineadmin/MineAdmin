<?php

namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Mine\Helper\ConsoleTable;
use Mine\Mine;
use Mine\MineCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class InstallLocalModuleCommand
 * @Command
 * @package System\Command
 */
class InstallLocalModuleCommand extends MineCommand
{
    /**
     * 安装命令
     * @var string
     */
    protected $name = 'mine:module-local';

    protected $database = [];

    protected $mine;

    public function configure()
    {
        parent::configure();
        $this->mine = make(Mine::Class);
        $this->setHelp('run "php bin/hyperf.php mine:module --name cms --option install"');
        $this->setDescription('install command of module MineAdmin');
        $this->addOption(
            'option', null, InputOption::VALUE_OPTIONAL,
            'input "--option list" show module list, "-option install" install module or "-option uninstall" uninstall module',
            'list'
        );
        $this->addOption(
            'name', null, InputOption::VALUE_OPTIONAL,
            'input module name or "list" command show module list',
        );
    }

    public function handle()
    {
        $name = $this->input->getOption('name');
        $option = $this->input->getOption('option');
        $modules = $this->mine->getModuleInfo();

        // 模块名不能叫list，list是展示模块列表
        if ($option === 'list') {
            $table = new ConsoleTable();
            $table->setHeader(['Name', 'Description', 'Version', "Install", "Enable"]);
            foreach ($modules as $mod) {
                $row = [
                  isset($mod['name']) ? $mod['name'] : 'Null',
                  isset($mod['description']) ? $mod['description'] : 'Null',
                  isset($mod['version']) ? $mod['version'] : 'Null',
                  isset($mod['installed']) && $mod['installed'] === true ? 'yes' : 'no',
                  isset($mod['enabled']) && $mod['enabled'] === true ? 'yes' : 'no',
                ];
                $table->addRow($row);
            }
            echo $table->render();
            exit;
        }

        // other module
        $name = ucfirst($name);
        if (!empty($name) && isset($modules[$name])) {
            if (empty($option)) {
                $this->line($this->getRedText('Please input the operation command for the module: -o install or -o uninstall'));
                exit;
            }
            if ($option === 'install') {
                $this->line(sprintf("Install complete, Please run it again \"%s\" command! ",$this->getGreenText('php bin/hyperf.php start')));
            }
            if ($option === 'uninstall') {
                $input = ucfirst($name) . ' uninstall';
                $answer = $this->ask(sprintf("You are now ready to unload the module for safety. Please input: %s", $this->getRedText($input)));
                if ($input !== $answer) {
                    $this->line('Input error');
                    exit;
                }
                $this->line(sprintf("Uninstall complete, Please run it again \"%s\" command! ",$this->getGreenText('php bin/hyperf.php start')));
            }
        } else {
            $this->line($this->getRedText(sprintf('The "%s" module does not exist....', $name)));
        }
    }
}
