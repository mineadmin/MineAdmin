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

namespace App\System\Crontab;

use App\System\Model\SystemApiLog;
use App\System\Model\SystemLoginLog;
use App\System\Model\SystemOperLog;
use App\System\Model\SystemQueueLog;
use Mine\Annotation\Transaction;

class ClearLogCrontab
{
    /**
     * 清理所有日志.
     */
    #[Transaction]
    public function execute(): string
    {
        SystemOperLog::truncate();
        SystemLoginLog::truncate();
        SystemQueueLog::truncate();
        SystemApiLog::truncate();

        return 'Clear logs successfully';
    }
}
