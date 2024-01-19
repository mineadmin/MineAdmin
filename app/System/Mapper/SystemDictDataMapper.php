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
        if (isset($params['type_id']) && filled($params['type_id'])) {
            $query->where('type_id', $params['type_id']);
        }
        if (isset($params['code']) && filled($params['code'])) {
            $query->where('code', $params['code']);
        }
        if (isset($params['value']) && filled($params['value'])) {
            $query->where('value', 'like', '%' . $params['value'] . '%');
        }
        if (isset($params['label']) && filled($params['label'])) {
            $query->where('label', 'like', '%' . $params['label'] . '%');
        }
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        return $query;
    }
}
