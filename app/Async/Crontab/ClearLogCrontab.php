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

namespace App\Async\Crontab;

use App\Model\Logs\LoginLog;
use App\Model\Logs\OperLog;
use App\Model\Logs\QueueLog;
use Mine\Annotation\Transaction;

class ClearLogCrontab
{
    /**
     * 清理所有日志.
     */
    #[Transaction]
    public function execute(): string
    {
        OperLog::truncate();
        LoginLog::truncate();
        QueueLog::truncate();

        return 'Clear logs successfully';
    }
}
