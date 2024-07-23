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

namespace App\Crontab;

use App\Model\System\LoginLog;
use App\Model\System\OperLog;
use App\Model\System\QueueLog;
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
