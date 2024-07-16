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

namespace App\Mapper\Setting;

use App\Model\Setting\CrontabLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class CrontabLogMapper extends AbstractMapper
{
    /**
     * @var CrontabLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = CrontabLog::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if ($params['crontab_id'] ?? false) {
            $query->where('crontab_id', $params['crontab_id']);
        }
        return $query;
    }
}
