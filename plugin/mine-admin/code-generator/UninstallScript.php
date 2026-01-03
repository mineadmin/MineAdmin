<?php

namespace Plugin\MineAdmin\CodeGenerator;

use Nette\Utils\FileSystem;
use Hyperf\Command\Concerns\InteractsWithIO;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class UninstallScript
{
    use InteractsWithIO;

    public function __construct()
    {
        global $argv;
        $this->input = new ArrayInput($argv);
        $this->output = new SymfonyStyle($this->input,new ConsoleOutput());
    }

    public function __invoke()
    {
        $app = ApplicationContext::getContainer()->get(ApplicationInterface::class);
        $app->setAutoExit(false);

        $isUninstall = $this->output->ask('确定卸载代码生成器吗?','no');

        if (in_array($isUninstall, ['y', 'Y', 'YES', 'yes'])) {

            $this->alert('将开始代码生成器，但后端 [plugin/mine-admin/code-generator] 将保留，请手动删除');

            $webRoot = BASE_PATH . '/web/src';
            if (is_dir($webRoot . '/plugins/mine-admin/code-generator')) {
                // 删除前端插件
                Filesystem::delete($webRoot . '/plugins/mine-admin/code-generator');
            }
            if (file_exists(BASE_PATH . '/plugin/mine-admin/code-generator/install.lock')) {
                // 删除插件 install.lock 文件
                Filesystem::delete(BASE_PATH . '/plugin/mine-admin/code-generator/install.lock');
            }
        }
    }
}