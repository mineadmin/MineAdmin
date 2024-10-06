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

use Hyperf\Command\Event\FailToHandle;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Logger\LoggerFactory;
use Mine\Support\Traits\Debugging;
use Psr\Log\LoggerInterface;

final class FailToHandleSubscriber implements ListenerInterface
{
    use Debugging;

    /** @phpstan-ignore-next-line */
    private LoggerInterface $logger;

    public function __construct(
        /** @phpstan-ignore-next-line */
        private readonly StdoutLoggerInterface $stdoutLogger,
        private readonly FormatterInterface $formatter,
        LoggerFactory $loggerFactory,
    ) {
        $this->logger = $loggerFactory->get('command');
    }

    public function listen(): array
    {
        return [
            FailToHandle::class,
        ];
    }

    /**
     * @param FailToHandle $event
     */
    public function process(object $event): void
    {
        $this->log(
            message: \sprintf('%s Command failed to handle, %s', $event->getCommand()->getName(), $this->formatter->format($event->getThrowable())),
            level: 'debug'
        );
    }
}
