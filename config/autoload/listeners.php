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
use Hyperf\ExceptionHandler\Listener\ErrorExceptionHandler;
use Mine\Kernel\Core\Subscriber\BootApplicationSubscriber;
use Mine\Kernel\Core\Subscriber\DbQueryExecutedSubscriber;
use Mine\Kernel\Core\Subscriber\FailToHandleSubscriber;
use Mine\Kernel\Core\Subscriber\QueueHandleSubscriber;
use Mine\Kernel\Core\Subscriber\ResumeExitCoordinatorSubscriber;
use Mine\Kernel\Core\Subscriber\Upload\UploadSubscriber;

return [
    ErrorExceptionHandler::class,
    // 默认文件上传
    UploadSubscriber::class,
    // 处理程序启动
    BootApplicationSubscriber::class,
    // 处理 sql 执行
    DbQueryExecutedSubscriber::class,
    // 处理命令异常
    FailToHandleSubscriber::class,
    // 处理 worker 退出
    ResumeExitCoordinatorSubscriber::class,
    // 处理队列
    QueueHandleSubscriber::class,
];
