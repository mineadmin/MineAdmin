<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemApiLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemApiMapper
 * @package App\System\Mapper
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
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (!empty($params['api_name'])) {
            $query->where('api_name', 'like', '%'.$params['api_name'].'%');
        }
        if (!empty($params['ip'])) {
            $query->where('ip', 'like', '%'.$params['ip'].'%');
        }
        if (!empty($params['access_name'])) {
            $query->where('access_name', 'like', '%'.$params['access_name'].'%');
        }
        if (!empty($params['access_time']) && is_array($params['access_time']) && count($params['access_time']) == 2) {
            $query->whereBetween(
                'access_time',
                [ $params['access_time'][0] . ' 00:00:00', $params['access_time'][1] . ' 23:59:59' ]
            );
        }
        return $query;
    }
}