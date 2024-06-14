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

namespace App\System\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\UserDelete;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UserDeleteListener.
 */
#[Listener]
class UserDeleteListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UserDelete::class,
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws \RedisException
     */
    public function process(object $event): void
    {
        $redis = redis();
        $prefix = config('cache.default.prefix') . 'Token:';
        $user = user();

        /**
         * @var UserDelete $event
         */
        foreach ($event->ids as $uid) {
            $token = $redis->get($prefix . $uid);
            $token && $user->getJwt()->logout($token);
            $redis->del([$prefix . $uid]);
        }
    }
}
