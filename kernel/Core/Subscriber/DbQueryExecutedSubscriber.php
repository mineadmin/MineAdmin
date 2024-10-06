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

use Hyperf\Collection\Arr;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Mine\Support\Traits\Debugging;
use Psr\Log\LoggerInterface;

final class DbQueryExecutedSubscriber implements ListenerInterface
{
    use Debugging;

    /** @phpstan-ignore-next-line */
    private LoggerInterface $logger;

    public function __construct(
        LoggerFactory $loggerFactory,
        /** @phpstan-ignore-next-line */
        private readonly StdoutLoggerInterface $stdoutLogger
    ) {
        $this->logger = $loggerFactory->get('sql');
    }

    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * @param QueryExecuted $event
     */
    public function process(object $event): void
    {
        if ($event instanceof QueryExecuted) {
            $sql = $event->sql;
            if (! Arr::isAssoc($event->bindings)) {
                $position = 0;
                foreach ($event->bindings as $value) {
                    $position = mb_strpos($sql, '?', $position);
                    if ($position === false) {
                        break;
                    }
                    $value = "'{$value}'";
                    $before = mb_substr($sql, 0, $position);
                    $after = mb_substr($sql, $position + 1, mb_strlen($sql) - $position);
                    $sql = $before . $value . $after;
                    $position += mb_strlen($value);
                }
            }
            $this->log(
                message: \sprintf('[%s:%s] %s', $event->connectionName, $event->time, $sql),
                level: 'info'
            );
        }
    }
}
