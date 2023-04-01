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
use Mine\Abstracts\AbstractMapper;

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

        // 数据库地址
        if (isset($params['db_host']) && $params['db_host'] !== '') {
            $query->where('db_host', 'like', '%'.$params['db_host'].'%');
        }

        // 数据库端口
        if (isset($params['db_port']) && $params['db_port'] !== '') {
            $query->where('db_port', 'like', '%'.$params['db_port'].'%');
        }

        // 数据库名称
        if (isset($params['db_name']) && $params['db_name'] !== '') {
            $query->where('db_name', 'like', '%'.$params['db_name'].'%');
        }

        // 数据库用户
        if (isset($params['db_user']) && $params['db_user'] !== '') {
            $query->where('db_user', 'like', '%'.$params['db_user'].'%');
        }

        // 数据库密码
        if (isset($params['db_pass']) && $params['db_pass'] !== '') {
            $query->where('db_pass', 'like', '%'.$params['db_pass'].'%');
        }

        // 数据库字符集
        if (isset($params['db_charset']) && $params['db_charset'] !== '') {
            $query->where('db_charset', 'like', '%'.$params['db_charset'].'%');
        }

        // 数据库字符序
        if (isset($params['db_collation']) && $params['db_collation'] !== '') {
            $query->where('db_collation', 'like', '%'.$params['db_collation'].'%');
        }

        return $query;
    }
}