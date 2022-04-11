<?php
declare(strict_types=1);

namespace App\System\Crontab;

use App\System\Model\SystemApiLog;
use App\System\Model\SystemLoginLog;
use App\System\Model\SystemOperLog;
use App\System\Model\SystemQueueLog;
use Mine\Annotation\Transaction;

class ClearLogCrontab
{
    /**
     * 清理所有日志
     * @Transaction
     * @return string
     */
    public function execute(): string
    {
        SystemOperLog::truncate();
        SystemLoginLog::truncate();
        SystemQueueLog::truncate();
        SystemApiLog::truncate();

        return 'Clear logs successfully';
    }
}