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
namespace Mine\Crontab\Mutex;

use Hyperf\Redis\RedisFactory;
use Hyperf\Collection\Arr;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Coroutine\Coroutine;
use Mine\Crontab\MineCrontab;

class RedisServerMutex implements ServerMutex
{
    /**
     * @var RedisFactory
     */
    private $redisFactory;

    /**
     * @var null|string
     */
    private $macAddress;

    public function __construct(RedisFactory $redisFactory)
    {
        $this->redisFactory = $redisFactory;

        $this->macAddress = $this->getMacAddress();
    }

    /**
     * Attempt to obtain a server mutex for the given crontab.
     * @param MineCrontab $crontab
     * @return bool
     */
    public function attempt(MineCrontab $crontab): bool
    {
        if ($this->macAddress === null) {
            return false;
        }

        $redis = $this->redisFactory->get($crontab->getMutexPool());
        $mutexName = $this->getMutexName($crontab);

        $result = (bool) $redis->set($mutexName, $this->macAddress, ['NX', 'EX' => $crontab->getMutexExpires()]);

        if ($result === true) {
            Coroutine::create(function () use ($crontab, $redis, $mutexName) {
                $exited = CoordinatorManager::until(Constants::WORKER_EXIT)->yield($crontab->getMutexExpires());
                $exited && $redis->del($mutexName);
            });
            return true;
        }

        return $redis->get($mutexName) === $this->macAddress;
    }

    /**
     * Get the server mutex for the given crontab.
     * @param MineCrontab $crontab
     * @return string
     */
    public function get(MineCrontab $crontab): string
    {
        return (string) $this->redisFactory->get($crontab->getMutexPool())->get(
            $this->getMutexName($crontab)
        );
    }

    protected function getMutexName(MineCrontab $crontab): string
    {
        return 'MineAdmin' . DIRECTORY_SEPARATOR . 'crontab-' . sha1($crontab->getName() . $crontab->getRule()) . '-sv';
    }

    protected function getMacAddress(): ?string
    {
        $macAddresses = swoole_get_local_mac();

        foreach (Arr::wrap($macAddresses) as $name => $address) {
            if ($address && $address !== '00:00:00:00:00:00') {
                return $name . ':' . str_replace(':', '', $address);
            }
        }

        return null;
    }
}
