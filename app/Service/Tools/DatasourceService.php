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

namespace App\Service\Tools;

use App\Repository\Tools\DatasourceRepository;
use Hyperf\Database\Model\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Helper\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 数据源管理服务类.
 */
class DatasourceService extends AbstractService
{
    /**
     * @var DatasourceRepository
     */
    public $repository;

    public function __construct(DatasourceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 测试数据库连接.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLink(array $params): bool
    {
        return (bool) $this->repository->getDataSourceTableList($this->read((int) $params['id'] ?? null));
    }

    /**
     * 获取数据源的表分页列表.
     */
    public function getDataSourceTablePageList(?array $params = [], bool $isScope = true): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 同步远程库表结构到本地.
     */
    public function syncRemoteTableStructToLocal(mixed $id, array $tableInfo): bool
    {
        if (empty($id)) {
            return false;
        }
        $sql = $this->repository->getCreateTableSql($this->read($id), $tableInfo['sourceName']);
        $sql = str_replace($tableInfo['sourceName'], $tableInfo['name'], $sql);
        if (stripos($sql, 'COMMENT') > -1) {
            $sql = preg_replace('/COMMENT=\'(.*?)+\'/', "COMMENT='{$tableInfo['comment']}'", $sql);
        } else {
            $sql .= ' COMMENT=' . $tableInfo['comment'];
        }
        return $this->repository->createTable($sql);
    }

    /**
     * 数组数据搜索器.
     * @return Collection
     */
    protected function handleArraySearch(\Hyperf\Collection\Collection $collect, array $params): \Hyperf\Collection\Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return Str::contains($row['Name'], $params['name']);
            });
        }
        if ($params['engine'] ?? false) {
            $collect = $collect->where('Engine', $params['engine']);
        }
        return $collect;
    }

    /**
     * 数组当前页数据返回之前处理器，默认对key重置.
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        $tables = [];
        foreach ($data as $item) {
            $tables[] = array_change_key_case((array) $item);
        }
        return $tables;
    }

    /**
     * 设置需要分页的数组数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getArrayData(array $params = []): array
    {
        if (empty($params['id'])) {
            return [];
        }
        return $this->repository->getDataSourceTableList($this->read((int) $params['id']));
    }
}
