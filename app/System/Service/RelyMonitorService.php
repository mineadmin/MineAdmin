<?php

declare(strict_types=1);
namespace App\System\Service;

use Hyperf\DbConnection\Db;
use Hyperf\Utils\Collection;
use Mine\Abstracts\AbstractService;

/**
 * 依赖服务处理类
 * Class RelyMonitorService
 * @package App\System\Service
 */
class RelyMonitorService extends AbstractService
{
    /**
     * 获取依赖包分页
     * @param array|null $params
     * @return array
     */
    public function getPackagePageList(?array $params = []): array
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
        return $collect;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        preg_match_all(
            "/([\w\-\/\d]+)\s+([v|V]?\d+\.\d+\.\d+)\s+(.+)/i",
            shell_exec('composer info'),
            $matches
        );

        $packages = [];
        foreach ($matches[1] as $k => $match) {
            $packages[] = [
                'name' => $match,
                'version' => $matches[2][$k],
                'description' => $matches[3][$k]
            ];
        }

        return $packages;
    }

    /**
     * 获取依赖包详细信息
     * @param string $name
     * @return array
     */
    public function getPackageDetail(string $name): array
    {
        if (empty($name)) {
            return [];
        }

        $content = shell_exec(sprintf('composer info %s', $name));
        preg_match_all("/(\w+)\s+:(.+)/i", $content, $matches);

        $infos = [];

        foreach ($matches[1] as $k => $match) {
            $infos[] = [
                'name'  => $match,
                'value' => $matches[2][$k],
            ];
        }

        preg_match_all('/\n(\w+)\n((.+)[\n]?)+/', $content, $matche);

        foreach ($matche[0] as $match) {
            $item = explode("\n", $match);
            array_pop($item);
            array_shift($item);
            $infos[] = [
                'name' => array_shift($item),
                'value' => implode('<br />', $item)
            ];
        }

        return $infos;
    }
}