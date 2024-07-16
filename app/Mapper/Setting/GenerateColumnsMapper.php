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

namespace App\Mapper\Setting;

use App\Model\Setting\GenerateColumns;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 生成业务字段信息表查询类
 * class GenerateColumnsMapper.
 */
class GenerateColumnsMapper extends AbstractMapper
{
    /**
     * @var GenerateColumns
     */
    public $model;

    public function assignModel()
    {
        $this->model = GenerateColumns::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if ($params['table_id'] ?? false) {
            $query->where('table_id', (int) $params['table_id']);
        }
        return $query;
    }
}
