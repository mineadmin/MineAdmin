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

namespace App\Repository\Tools;

use App\Model\Tools\GenerateColumns;
use Hyperf\Database\Model\Builder;
use App\Kernel\IRepository\AbstractRepository;

/**
 * 生成业务字段信息表查询类
 * class GenerateColumnsRepository.
 */
class GenerateColumnsRepository extends AbstractRepository
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
