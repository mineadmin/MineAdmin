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

use App\System\Model\SystemDictData;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemUserMapper.
 */
class SystemDictDataMapper extends AbstractMapper
{
    /**
     * @var SystemDictData
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemDictData::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (! empty($params['type_id'])) {
            $query->where('type_id', $params['type_id']);
        }
        if (! empty($params['code'])) {
            $query->where('code', $params['code']);
        }
        if (! empty($params['value'])) {
            $query->where('value', 'like', '%' . $params['value'] . '%');
        }
        if (! empty($params['label'])) {
            $query->where('label', 'like', '%' . $params['label'] . '%');
        }
        if (! empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        return $query;
    }
}
