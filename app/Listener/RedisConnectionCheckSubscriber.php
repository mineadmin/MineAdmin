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

namespace App\Listener;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

#[Listener]
class RedisConnectionCheckSubscriber implements ListenerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly LoggerInterface $logger
    ) {}

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof BootApplication) {
            $this->checkRedisConnection();
        }
    }

    private function checkRedisConnection(): void
    {
        try {
            $redis = $this->container->get(RedisFactory::class)->get('default');
            $result = $redis->ping();
            if ($result !== 'PONG') {
                $this->logger->error('Redis connection failed: Invalid ping response');
                $this->container->get(StdoutLoggerInterface::class)->error('Redis connection failed: Invalid ping response');
            }
        } catch (\Throwable $e) {
            $this->logger->error('Redis connection failed: ' . $e->getMessage());
            $this->container->get(StdoutLoggerInterface::class)->error('Redis connection failed, please check redis config in .env file');
        }
    }
}
