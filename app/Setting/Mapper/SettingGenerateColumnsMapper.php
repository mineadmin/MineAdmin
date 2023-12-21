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

use App\Setting\Model\SettingGenerateColumns;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 生成业务字段信息表查询类
 * Class SettingGenerateColumnsMapper.
 */
class SettingGenerateColumnsMapper extends AbstractMapper
{
    /**
     * @var SettingGenerateColumns
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingGenerateColumns::class;
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
