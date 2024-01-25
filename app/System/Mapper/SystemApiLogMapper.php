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

use App\System\Model\SystemApiLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemApiMapper.
 */
class SystemApiLogMapper extends AbstractMapper
{
    /**
     * @var SystemApiLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemApiLog::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['api_name']) && filled($params['api_name'])) {
            $query->where('api_name', 'like', '%' . $params['api_name'] . '%');
        }
        if (isset($params['ip']) && filled($params['ip'])) {
            $query->where('ip', 'like', '%' . $params['ip'] . '%');
        }
        if (isset($params['access_name']) && filled($params['access_name'])) {
            $query->where('access_name', 'like', '%' . $params['access_name'] . '%');
        }
        if (isset($params['access_time']) && filled($params['access_time']) && is_array($params['access_time']) && count($params['access_time']) == 2) {
            $query->whereBetween(
                'access_time',
                [$params['access_time'][0] . ' 00:00:00', $params['access_time'][1] . ' 23:59:59']
            );
        }
        return $query;
    }
}
