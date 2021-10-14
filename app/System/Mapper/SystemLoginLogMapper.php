<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemLoginLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemUserMapper
 * @package App\System\Mapper
 */
class SystemLoginLogMapper extends AbstractMapper
{
    /**
     * @var SystemLoginLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemLoginLog::class;
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
        if (isset($params['username'])) {
            $query->where('username', 'like', '%'.$params['username'].'%');
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['method'])) {
            $query->where('method', $params['method']);
        }
        if (isset($params['minDate']) && isset($params['maxDate'])) {
            $query->whereBetween(
                'login_time',
                [$params['minDate'] . ' 00:00:00', $params['maxDate'] . ' 23:59:59']
            );
        }
        return $query;
    }
}