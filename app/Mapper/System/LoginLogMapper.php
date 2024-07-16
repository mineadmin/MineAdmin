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

namespace App\Mapper\System;

use App\Model\System\LoginLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class UserMapper.
 */
class LoginLogMapper extends AbstractMapper
{
    /**
     * @var LoginLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = LoginLog::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['ip']) && filled($params['ip'])) {
            $query->where('ip', $params['ip']);
        }

        if (isset($params['username']) && filled($params['username'])) {
            $query->where('username', 'like', '%' . $params['username'] . '%');
        }

        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['login_time']) && filled($params['login_time']) && is_array($params['login_time']) && count($params['login_time']) == 2) {
            $query->whereBetween(
                'login_time',
                [$params['login_time'][0] . ' 00:00:00', $params['login_time'][1] . ' 23:59:59']
            );
        }
        return $query;
    }
}
