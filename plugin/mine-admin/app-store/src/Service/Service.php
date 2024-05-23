<?php

namespace Plugin\MineAdmin\AppStore\Service;

use Hyperf\Contract\ApplicationInterface;
use Mine\AppStore\Plugin;
use Mine\Exception\MineException;
use Mine\AppStore\Service\Impl\AppStoreServiceImpl;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;


class Service
{

    /**
     * @param array $params
     * @return bool
     */
    public function download(array $params): bool
    {
        if (empty($params['space']) || empty($params['identifier']) || empty($params['version'])) {
            throw new MineException('请检查space、identifier、version参数是否正确');
        }

        $service = make(AppStoreServiceImpl::class);

        if (! is_dir(BASE_PATH . '/plugin/' . $params['space'] . '/' . $params['identifier'])) {
            $result = $service->download($params['space'], $params['identifier'], $params['version']);
            if (! $result) {
                throw new MineException('应用下载失败');
            }
        }

        return true;
    }

    /**
     * @param array $params
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function install(array $params): bool
    {
        if (empty($params['space']) || empty($params['identifier']) || empty($params['version'])) {
            throw new MineException('请检查space、identifier、version参数是否正确');
        }

        $path = BASE_PATH . '/plugin/' . $params['space'] . '/' . $params['identifier'];

        if ( file_exists($path . '/install.lock') ) {
            throw new MineException('应用已经安装过');
        }

        $pluginName =  $params['space'] . "/" . $params['identifier'];
        try {
            Plugin::forceRefreshJsonPath();
            Plugin::install($pluginName);
        } catch (\RuntimeException $e) {
            throw new MineException($e->getMessage());
        }

        return true;
    }

    public function unInstall(array $params): bool
    {
        if (empty($params['space']) || empty($params['identifier']) || empty($params['version'])) {
            throw new MineException('请检查space、identifier、version参数是否正确');
        }

        $path = BASE_PATH . '/plugin/' . $params['space'] . '/' . $params['identifier'];

        if (! file_exists($path . '/install.lock') ) {
            throw new MineException('应用并未安装');
        }

        $pluginName =  $params['space'] . "/" . $params['identifier'];
        try {
            Plugin::forceRefreshJsonPath();
            Plugin::uninstall($pluginName);
        } catch (\RuntimeException $e) {
            throw new MineException($e->getMessage());
        }
        return true;
    }

    public function getLocalAppInstallList(): array
    {
        $list = Plugin::getPluginJsonPaths();

        $items = [];
        foreach ($list as $splFileInfo) {
            $info = Plugin::read($splFileInfo->getRelativePath());
            if (! empty($info)) {
                $items[$info['name']] = [
                    'status' => $info['status'],
                    'version' => $info['version'],
                ];
            }
        }
        return $items;
    }

    public function getLocalAppList(): array
    {
        return [];
    }
}