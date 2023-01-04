<?php

/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

namespace Mine\Crontab;

use Swoole\Server;
use Hyperf\Crontab\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\ProcessManager;
use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Strategy\StrategyInterface;
use Hyperf\Crontab\Event\CrontabDispatcherStarted;

class MineCrontabProcess extends AbstractProcess
{
    /**
     * @var string
     */
    public string $name = 'MineAdmin Crontab';

    /**
     * @var Server
     */
    private $server;

    /**
     * @var MineCrontabScheduler
     */
    private $scheduler;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    /**
     * @var MineCrontabManage
     */
    #[Inject]
    protected MineCrontabManage $mineCrontabManage;

    /**
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->scheduler = $container->get(MineCrontabScheduler::class);
        $this->strategy = $container->get(MineCrontabStrategy::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function bind($server): void
    {
        $this->server = $server;
        parent::bind($server);
    }

    /**
     * 是否自启进程
     * @param \Swoole\Coroutine\Server|\Swoole\Server $server
     * @return bool
     */
    public function isEnable($server): bool
    {
        if (!file_exists(BASE_PATH . '/.env')) {
            return false;
        }
        return true;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface

     */
    public function handle(): void
    {
        $this->event->dispatch(new CrontabDispatcherStarted());
        while (ProcessManager::isRunning()) {
            $this->sleep();
            $crontabs = $this->scheduler->schedule();
            while (!$crontabs->isEmpty()) {
                /**
                 * @var MineCrontab $crontab
                 */
                $crontab =  $crontabs->dequeue();
                $this->strategy->dispatch($crontab);
            }
        }
    }

    private function sleep()
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        $this->logger->debug('MineAdmin Crontab dispatcher sleep ' . $sleep . 's.');
        $sleep > 0 && \Swoole\Coroutine::sleep($sleep);
    }
}
