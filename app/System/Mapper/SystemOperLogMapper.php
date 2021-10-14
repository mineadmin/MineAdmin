<?php


namespace App\System\Mapper;


use App\System\Model\SystemOperLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class SystemOperLogMapper extends AbstractMapper
{
    /**
     * @var SystemOperLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemOperLog::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['ip'])) {
            $query->where('ip', $params['ip']);
        }
        if (isset($params['service_name'])) {
            $query->where('service_name', 'like', '%'.$params['service_name'].'%');
        }
        if (isset($params['username'])) {
            $query->where('username', 'like', '%'.$params['username'].'%');
        }
        if (isset($params['method'])) {
            $query->where('method', $params['method']);
        }
        if (isset($params['minDate']) && isset($params['maxDate'])) {
            $query->whereBetween(
                'created_at',
                [$params['minDate'] . ' 00:00:00', $params['maxDate'] . ' 23:59:59']
            );
        }
        return $query;
    }

}