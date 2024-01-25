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

namespace App\Setting\Mapper;

use App\Setting\Model\SettingDatasource;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Exception\MineException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 数据源管理Mapper类.
 */
class SettingDatasourceMapper extends AbstractMapper
{
    /**
     * @var SettingDatasource
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingDatasource::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 数据源名称
        if (isset($params['source_name']) && filled($params['source_name'])) {
            $query->where('source_name', 'like', '%' . $params['source_name'] . '%');
        }

        return $query;
    }

    /**
     * 测试数据库连接.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDataSourceTableList(array|object $params): array
    {
        try {
            return $this->connectionDb($params)->query('SHOW TABLE STATUS')->fetchAll();
        } catch (\Throwable $e) {
            throw new MineException($e->getMessage(), 500);
        }
    }

    /**
     * 获取创建表结构SQL.
     */
    public function getCreateTableSql(array|object $params, string $tableName): string
    {
        try {
            return $this->connectionDb($params)->query(
                sprintf('SHOW CREATE TABLE %s', $tableName)
            )->fetch()['Create Table'];
        } catch (\Throwable $e) {
            throw new MineException($e->getMessage(), 500);
        }
    }

    /**
     * 通过SQL创建表.
     */
    public function createTable(string $sql): bool
    {
        return Db::connection('default')->getPdo()->exec($sql) > 0;
    }

    public function connectionDb(array|object $params): \PDO
    {
        return new \PDO($params['dsn'], $params['username'], $params['password'], [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }
}
