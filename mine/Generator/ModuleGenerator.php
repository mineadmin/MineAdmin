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

namespace Mine\Generator;

use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Mine;

class ModuleGenerator extends MineGenerator
{
    /**
     * @var array
     */
    protected array $moduleInfo;

    /**
     * 设置模块信息
     * @param array $moduleInfo
     * @return $this
     */
    public function setModuleInfo(array $moduleInfo): ModuleGenerator
    {
        $this->moduleInfo = $moduleInfo;
        return $this;
    }

    /**
     * 生成模块基础架构
     */
    public function createModule(): bool
    {
        if (! ($this->moduleInfo['name'] ?? false)) {
            throw new \RuntimeException('模块名称为空');
        }

        $this->moduleInfo['name'] = ucfirst($this->moduleInfo['name']);

        $mine = new Mine;
        $mine->scanModule();

        if (! empty($mine->getModuleInfo($this->moduleInfo['name']))) {
            throw new \RuntimeException('同名模块已存在');
        }

        $appPath = BASE_PATH . '/app/';
        $modulePath = $appPath . $this->moduleInfo['name'] . '/';

        /** @var Filesystem $filesystem */
        $filesystem = make(Filesystem::class);
        $filesystem->makeDirectory($appPath . $this->moduleInfo['name']);

        foreach ($this->getGeneratorDirs() as $dir) {
            $filesystem->makeDirectory($modulePath . $dir);
        }

        $this->createConfigJson($filesystem);

        return true;
    }

    /**
     * 创建模块JSON文件
     */
    protected function createConfigJson(Filesystem $filesystem)
    {
        $json = $filesystem->sharedGet($this->getStubDir() . 'config.stub');

        $content = str_replace(
            ['{NAME}','{LABEL}','{DESCRIPTION}', '{VERSION}'],
            [
                $this->moduleInfo['name'],
                $this->moduleInfo['label'],
                $this->moduleInfo['description'],
                $this->moduleInfo['version']
            ],
            $json
        );

        $filesystem->put(BASE_PATH . '/app/' .$this->moduleInfo['name'] . '/config.json', $content);
    }

    /**
     * 生成的目录列表
     */
    protected function getGeneratorDirs(): array
    {
        return [
            'Controller',
            'Model',
            'Listener',
            'Request',
            'Service',
            'Mapper',
            'Database',
            'Middleware',
        ];
    }
}