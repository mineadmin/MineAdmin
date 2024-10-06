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

namespace Mine\Core\Subscriber;

use Hyperf\AsyncQueue\AnnotationJob;
use Hyperf\AsyncQueue\Event\AfterHandle;
use Hyperf\AsyncQueue\Event\BeforeHandle;
use Hyperf\AsyncQueue\Event\Event;
use Hyperf\AsyncQueue\Event\FailedHandle;
use Hyperf\AsyncQueue\Event\RetryHandle;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Logger\LoggerFactory;
use Mine\Support\Traits\Debugging;
use Psr\Log\LoggerInterface;

final class QueueHandleSubscriber implements ListenerInterface
{
    use Debugging;

    protected LoggerInterface $logger;

    public function __construct(
        /** @phpstan-ignore-next-line */
        private readonly StdoutLoggerInterface $stdoutLogger,
        private readonly FormatterInterface $formatter,
        LoggerFactory $loggerFactory,
    ) {
        $this->logger = $loggerFactory->get('queue');
    }

    public function listen(): array
    {
        return [
            AfterHandle::class,
            BeforeHandle::class,
            FailedHandle::class,
            RetryHandle::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof Event) {
            $job = $event->getMessage()->job();
            $jobClass = $job::class;
            if ($job instanceof AnnotationJob) {
                $jobClass = \sprintf('Job[%s@%s]', $job->class, $job->method);
            }
            $date = date('Y-m-d H:i:s');
            $format = match (true) {
                $event instanceof BeforeHandle => '[%s] Processing %s.',
                $event instanceof AfterHandle => '[%s] Processed %s.',
                $event instanceof FailedHandle => '[%s] Failed %s.' . \PHP_EOL . $this->formatter->format($event->getThrowable()),
                $event instanceof RetryHandle => '[%s] Retried %s.',
                default => null,
            };
            $this->log(
                message: \sprintf($format, $date, $jobClass),
                level: 'debug'
            );
        }
    }
}
