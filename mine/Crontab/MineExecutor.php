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

use App\Setting\Service\SettingCrontabLogService;
use Carbon\Carbon;
use Closure;
use Hyperf\Contract\ApplicationInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\LoggerInterface;
use Mine\Crontab\Mutex\RedisServerMutex;
use Mine\Crontab\Mutex\RedisTaskMutex;
use Mine\Crontab\Mutex\ServerMutex;
use Mine\Crontab\Mutex\TaskMutex;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;
use Swoole\Timer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use App\Setting\Model\SettingCrontab;

class MineExecutor
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    /**
     * @var TaskMutex
     */
    protected $taskMutex;

    /**
     * @var ServerMutex
     */
    protected $serverMutex;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        if ($container->has(LoggerInterface::class)) {
            $this->logger = $container->get(LoggerInterface::class);
        } elseif ($container->has(StdoutLoggerInterface::class)) {
            $this->logger = $container->get(StdoutLoggerInterface::class);
        }
    }

    /**
     * 执行定时任务
     * @param MineCrontab $crontab
     * @param bool $run
     * @return bool|null
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function execute(MineCrontab $crontab, bool $run = false): ?bool
    {
        if ((! $crontab instanceof MineCrontab || ! $crontab->getExecuteTime()) && !$run) {
            return null;
        }
        $diff = 0;
        !$run && $diff = $crontab->getExecuteTime()->diffInRealSeconds(new Carbon());
        $callback = null;
        switch ($crontab->getType()) {
            case SettingCrontab::CLASS_CRONTAB:
                $class = $crontab->getCallback();
                $method = 'execute';
                $parameters = $crontab->getParameter() ?: null;
                if ($class && class_exists($class) && method_exists($class, $method)) {
                    $callback = function () use ($class, $method, $parameters, $crontab) {
                        $runnable = function () use ($class, $method, $parameters, $crontab) {
                            try {
                                $result = true;
                                $res = null;
                                $instance = make($class);
                                if ($parameters && is_array($parameters)) {
                                    $res = $instance->{$method}(...$parameters);
                                } else {
                                    $res = $instance->{$method}();
                                }
                            } catch (\Throwable $throwable) {
                                $result = false;
                            } finally {
                                $this->logResult($crontab, $result, isset($throwable) ? $throwable->getMessage() : $res);
                            }
                        };

                        Coroutine::create($this->decorateRunnable($crontab, $runnable));
                    };
                }
                break;
            case SettingCrontab::COMMAND_CRONTAB:
                $command = ['command' => $crontab->getCallback()];
                $parameter = $crontab->getParameter() ?: '{}';
                $input = make(ArrayInput::class, array_merge($command, json_decode($parameter, true)));
                $output = make(NullOutput::class);
                $application = $this->container->get(ApplicationInterface::class);
                $application->setAutoExit(false);
                $callback = function () use ($application, $input, $output, $crontab) {
                    $runnable = function () use ($application, $input, $output, $crontab) {
                        $result = $application->run($input, $output);
                        $this->logResult($crontab, $result === 0, $result);
                    };
                    $this->decorateRunnable($crontab, $runnable)();
                };
                break;
            case SettingCrontab::URL_CRONTAB:
                $clientFactory = $this->container->get(ClientFactory::class);
                $client = $clientFactory->create();
                $callback = function () use ($client, $crontab) {
                    $runnable = function () use ($client, $crontab) {
                        try {
                            $response = $client->get($crontab->getCallback());
                            $result = $response->getStatusCode() === 200;
                        } catch (\Throwable $throwable) {
                            $result = false;
                        }
                        $this->logResult(
                            $crontab,
                            $result,
                            (!$result && isset($response)) ? $response->getBody() : ''
                        );
                    };
                    $this->decorateRunnable($crontab, $runnable)();
                };
                break;
            case SettingCrontab::EVAL_CRONTAB:
                $callback = function () use ($crontab) {
                    $runnable = function () use ($crontab) {
                        $result = true;
                        try {
                            eval($crontab->getCallback());
                        } catch (\Throwable $throwable) {
                            $result = false;
                        }
                        $this->logResult($crontab, $result, isset($throwable) ? $throwable->getMessage() : '');
                    };
                    $this->decorateRunnable($crontab, $runnable)();
                };
                break;
        }
        $callback && Timer::after($diff > 0 ? $diff * 1000 : 1, $callback);

        return true;
    }

    protected function runInSingleton(MineCrontab $crontab, Closure $runnable): Closure
    {
        return function () use ($crontab, $runnable) {
            $taskMutex = $this->getTaskMutex();

            if ($taskMutex->exists($crontab) || ! $taskMutex->create($crontab)) {
                $this->logger->info(sprintf('Crontab task [%s] skipped execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
                return;
            }

            try {
                $runnable();
            } finally {
                $taskMutex->remove($crontab);
            }
        };
    }

    protected function getTaskMutex(): TaskMutex
    {
        if (! $this->taskMutex) {
            $this->taskMutex = $this->container->has(TaskMutex::class)
                ? $this->container->get(TaskMutex::class)
                : $this->container->get(RedisTaskMutex::class);
        }
        return $this->taskMutex;
    }

    protected function runOnOneServer(MineCrontab $crontab, Closure $runnable): Closure
    {
        return function () use ($crontab, $runnable) {
            $taskMutex = $this->getServerMutex();

            if (! $taskMutex->attempt($crontab)) {
                $this->logger->info(sprintf('Crontab task [%s] skipped execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
                return;
            }

            $runnable();
        };
    }

    protected function getServerMutex(): ServerMutex
    {
        if (! $this->serverMutex) {
            $this->serverMutex = $this->container->has(ServerMutex::class)
                ? $this->container->get(ServerMutex::class)
                : $this->container->get(RedisServerMutex::class);
        }
        return $this->serverMutex;
    }

    protected function decorateRunnable(MineCrontab $crontab, Closure $runnable): Closure
    {
        if ($crontab->isSingleton()) {
            $runnable = $this->runInSingleton($crontab, $runnable);
        }

        if ($crontab->isOnOneServer()) {
            $runnable = $this->runOnOneServer($crontab, $runnable);
        }

        return $runnable;
    }

    protected function logResult(MineCrontab $crontab, bool $isSuccess, $result = '')
    {
        if ($this->logger) {
            if ($isSuccess) {
                $this->logger->info(sprintf('Crontab task [%s] executed successfully at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
            } else {
                $this->logger->error(sprintf('Crontab task [%s] failed execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
            }
        }
        $logService = $this->container->get(SettingCrontabLogService::class);
        $data = [
            'crontab_id' => $crontab->getCrontabId(),
            'name' => $crontab->getName(),
            'target' => $crontab->getCallback(),
            'parameter' => $crontab->getParameter(),
            'exception_info' => $result,
            'status' => $isSuccess ? '0' : '1',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $logService->save($data);
    }
}