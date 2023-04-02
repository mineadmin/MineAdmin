<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Setting\Service;

use App\Setting\Mapper\SettingDatasourceMapper;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 数据源管理服务类
 */
class SettingDatasourceService extends AbstractService
{
    /**
     * @var SettingDatasourceMapper
     */
    public $mapper;

    public function __construct(SettingDatasourceMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 测试数据库连接
     * @param array $params
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testLink(array $params): bool
    {
        return (bool)$this->mapper->getDataSourceTableList($this->read((int) $params['id'] ?? null));
    }

    /**
     * 获取数据源的表分页列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getDataSourceTablePageList(?array $params = [], bool $isScope = true): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 数组数据搜索器
     * @param \Hyperf\Utils\Collection $collect
     * @param array $params
     * @return Collection
     */
    protected function handleArraySearch(\Hyperf\Utils\Collection $collect, array $params): \Hyperf\Utils\Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row->Name, $params['name']);
            });
        }
        if ($params['engine'] ?? false) {
            $collect = $collect->where('Engine', $params['engine']);
        }
        return $collect;
    }

    /**
     * 数组当前页数据返回之前处理器，默认对key重置
     * @param array $data
     * @param array $params
     * @return array
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        $tables = [];
        foreach ($data as $item) {
            $tables[] = array_change_key_case((array)$item);
        }
        return $tables;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getArrayData(array $params = []): array
    {
        if (empty($params['id'])) return [];
        return $this->mapper->getDataSourceTableList($this->read((int) $params['id']));
    }
}