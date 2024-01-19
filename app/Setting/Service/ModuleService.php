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

namespace App\Setting\Service;

use Hyperf\Collection\Collection;
use Hyperf\Config\Annotation\Value;
use Hyperf\Contract\ApplicationInterface;
use Hyperf\Database\Migrations\Migrator;
use Hyperf\Support\Filesystem\Filesystem;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Generator\ModuleGenerator;
use Mine\Helper\Str;
use Mine\Interfaces\ServiceInterface\ModuleServiceInterface;
use Mine\Mine;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

#[DependProxy(values: [ModuleServiceInterface::class])]
class ModuleService extends AbstractService implements ModuleServiceInterface
{
    protected Mine $mine;

    #[Value('cache.default.prefix')]
    protected ?string $prefix = null;

    public function __construct(Mine $mine)
    {
        $this->mine = $mine;
        $this->setModuleCache();
    }

    /**
     * 获取表状态分页列表.
     */
    public function getPageList(?array $params = [], bool $isScope = true): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 创建模块.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createModule(array $moduleInfo): bool
    {
        /** @var ModuleGenerator $moduleGen */
        $moduleGen = make(ModuleGenerator::class);
        $moduleGen->setModuleInfo($moduleInfo)->createModule();
        $this->setModuleCache();
        return true;
    }

    /**
     * 执行模块安装.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function installModuleData(string $name): bool
    {
        try {
            $migrateCommand = ['command' => 'mine:migrate-run', 'name' => $name];
            $seedCommand = ['command' => 'mine:seeder-run', 'name' => $name];
            $application = container()->get(ApplicationInterface::class);
            $application->setAutoExit(false);
            $application->run(new ArrayInput($migrateCommand), new NullOutput());
            $application->run(new ArrayInput($seedCommand), new NullOutput());
            $this->setModuleCache();
            return true;
        } catch (\Throwable $e) {
            console()->error($e->getMessage());
            return false;
        }
    }

    /**
     * 卸载模块.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function uninstallModule(string $name): bool
    {
        try {
            $migrate = container()->get(Migrator::class);
            $path = BASE_PATH . '/app/' . $name . '/Database/Migrations';
            $migrate->rollback([$path]);
            is_dir($path . '/Update') && $migrate->rollback([$path . '/Update']);
            $this->deleteModule($name);
            $this->setModuleCache();
            return true;
        } catch (\Throwable $e) {
            console()->error($e->getMessage());
            return false;
        }
    }

    /**
     * 删除模块.
     */
    public function deleteModule(string $name): bool
    {
        /** @var Filesystem $filesystem */
        $filesystem = make(Filesystem::class);
        $modulePath = BASE_PATH . '/app/' . ucfirst($name);
        return $filesystem->deleteDirectory($modulePath);
    }

    /**
     * 缓存模块信息.
     * @param null|string $moduleName 模块名
     * @param array $data 模块数据
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setModuleCache(?string $moduleName = null, array $data = []): void
    {
        $key = $this->prefix . 'modules';
        $this->mine->scanModule();
        $modules = $this->mine->getModuleInfo();
        if (! empty($moduleName)) {
            $modules[$moduleName] = $data;
        }
        redis()->set($key, serialize($modules));
    }

    /**
     * 获取模块缓存信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getModuleCache(?string $moduleName = null): array
    {
        $key = $this->prefix . 'modules';
        $redis = redis();
        if ($data = $redis->get($key)) {
            $data = unserialize($data);
            return ! empty($moduleName) && isset($data[$moduleName]) ? $data[$moduleName] : $data;
        }
        $this->setModuleCache();
        $this->mine->scanModule();
        return $this->mine->getModuleInfo();
    }

    /**
     * 启停用模块.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function modifyStatus(array $data): bool
    {
        $modules = make(Mine::class)->getModuleInfo();
        if (isset($modules[$data['name']])) {
            $filePath = BASE_PATH . '/app/' . $data['name'] . '/config.json';
            $status = $data['status'] ? 'true' : 'false';
            $content = preg_replace(
                '/\"enabled\":\s(true|false),/',
                '"enabled": ' . $status . ',',
                file_get_contents($filePath)
            );
            $result = (bool) file_put_contents($filePath, $content);
            $this->setModuleCache();
            return $result;
        }
        return false;
    }

    /**
     * 数组数据搜索器.
     */
    protected function handleArraySearch(Collection $collect, array $params): Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return Str::contains($row['name'], $params['name']);
            });
        }

        if ($params['label'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return Str::contains($row['label'], $params['label']);
            });
        }
        return $collect;
    }

    /**
     * 设置需要分页的数组数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getArrayData(array $params = []): array
    {
        return $this->getModuleCache();
    }
}
