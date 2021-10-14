<?php

declare(strict_types=1);
namespace Mine\Crontab;

use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;

class MineCrontabStrategy
{
    /**
     * @Inject
     * @var MineCrontabManage
     */
    protected $mineCrontabManage;

    /**
     * @Inject
     * @var MineExecutor
     */
    protected $executor;

    /**
     * @param MineCrontab $crontab
     */
    public function dispatch(MineCrontab $crontab)
    {
        co(function() use($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && \Swoole\Coroutine::sleep($wait);
                $this->executor->execute($crontab);
            }
        });
    }

    /**
     * 执行一次
     * @param MineCrontab $crontab
     */
    public function executeOnce(MineCrontab $crontab)
    {
        co(function() use($crontab) {
            $this->executor->execute($crontab);
        });
    }
}