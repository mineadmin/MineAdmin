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

namespace App\System\Mapper;

use App\System\Model\SystemApiColumn;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemApiColumnMapper.
 */
class SystemApiColumnMapper extends AbstractMapper
{
    /**
     * @var SystemApiColumn
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemApiColumn::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 接口ID
        if (! empty($params['api_id'])) {
            $query->where('api_id', '=', $params['api_id']);
        }

        // 字段类型
        if (! empty($params['type'])) {
            $query->where('type', '=', $params['type']);
        }

        // 字段名称
        if (! empty($params['name'])) {
            $query->where('name', '=', $params['name']);
        }

        // 数据类型
        if (! empty($params['data_type'])) {
            $query->where('data_type', '=', $params['data_type']);
        }

        // 是否必填 1 非必填 2 必填
        if (! empty($params['is_required'])) {
            $query->where('is_required', '=', $params['is_required']);
        }

        // 状态 (1正常 2停用)
        if (! empty($params['status'])) {
            $query->where('status', '=', $params['status']);
        }
        return $query;
    }
}
