<?php

declare(strict_types=1);
namespace App\System\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\UserDelete;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UserDeleteListener
 * @package App\System\Listener
 */
#[Listener]
class UserDeleteListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UserDelete::class
        ];
    }

    /**
     * @param object $event
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

        /* @var $event UserDelete */
        foreach ($event->ids as $uid) {
            $token = $redis->get($prefix . $uid);
            $token && $user->getJwt()->logout($token);
            $redis->del([$prefix . $uid]);
        }
    }
}