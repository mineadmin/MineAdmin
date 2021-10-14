<?php

declare(strict_types=1);
namespace App\Setting\Service;

use Hyperf\Utils\Collection;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Abstracts\AbstractService;
use Mine\Generator\ModuleGenerator;
use Mine\Mine;

class ModuleService extends AbstractService
{
    /**
     * @var Mine
     */
    public $mine;

    public function __construct(Mine $mine)
    {
        $this->mine = $mine;
    }
    /**
     * 获取表状态分页列表
     * @param array|null $params
     * @return array
     */
    public function getPageList(?array $params = []): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 数组数据搜索器
     * @param Collection $collect
     * @param array $params
     * @return Collection
     */
    protected function handleArraySearch(Collection $collect, array $params): Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row['name'], $params['name']);
            });
        }

        if ($params['label'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row['label'], $params['label']);
            });
        }
        return $collect;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        // 先扫描模块
        $this->mine->scanModule();
        return $this->mine->getModuleInfo();
    }

    /**
     * 创建模块
     * @param array $moduleInfo
     * @return bool
     */
    public function createModule(array $moduleInfo): bool
    {
        /** @var ModuleGenerator $moduleGen */
        $moduleGen = make(ModuleGenerator::class);
        $moduleGen->setModuleInfo($moduleInfo)->createModule();
        return true;
    }

    /**
     * 删除模块
     * @param string $name
     * @return bool
     */
    public function deleteModule(string $name): bool
    {
        /** @var Filesystem $filesystem */
        $filesystem = make(Filesystem::class);
        $modulePath = BASE_PATH . '/app/' . ucfirst($name);
        return $filesystem->deleteDirectory($modulePath);
    }
}