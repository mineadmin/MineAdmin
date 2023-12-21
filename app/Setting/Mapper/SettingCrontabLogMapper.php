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

use App\Setting\Model\SettingCrontabLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class SettingCrontabLogMapper extends AbstractMapper
{
    /**
     * @var SettingCrontabLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingCrontabLog::class;
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
