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

namespace App\Setting\Mapper;

use App\Setting\Model\SettingDatasource;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Exception\MineException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 数据源管理Mapper类
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
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 数据源名称
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', 'like', '%'.$params['name'].'%');
        }

        return $query;
    }

    /**
     * 测试数据库连接
     * @param Object|array $params
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDataSourceTableList(Object|array $params): array
    {
        $config = container()->get(\Hyperf\Contract\ConfigInterface::class);
        $dataSource = $config->get('databases.default');

        $dataSource['host'] = $params['db_host'];
        $dataSource['database'] = $params['db_name'];
        $dataSource['port'] = $params['db_port'];
        $dataSource['username'] = $params['db_user'];
        $dataSource['password'] = $params['db_pass'];
        $dataSource['charset'] = $params['db_charset'];
        $dataSource['collation'] = $params['db_collation'];

        $config->set('databases.testDataSource', $dataSource);
        try {
            $query = Db::connection('testDataSource');
            return $query->select($query->raw('show tables')->getValue());
        } catch (\Throwable $e) {
            throw new MineException($e->getMessage(), 500);
        }
    }
}