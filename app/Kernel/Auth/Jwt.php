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

namespace App\Kernel\Auth;

use Carbon\Carbon;
use Hyperf\Cache\CacheManager;
use Hyperf\Cache\Driver\DriverInterface;
use Hyperf\Collection\Arr;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Psr\Clock\ClockInterface as Clock;

class Jwt implements JwtInterface
{
    public function __construct(
        private readonly array $config,
        private readonly CacheManager $cacheManager
    ) {}

    public function builder(array $claims): UnencryptedToken
    {
        return $this->getJwtFacade()->issue(
            $this->getSigner(),
            $this->getSigningKey(),
            function (Builder $builder, \DateTimeImmutable $immutable) use ($claims) {
                $builder->issuedAt($immutable);
                foreach ($claims as $key => $value) {
                    $builder = $builder->withClaim($key, $value);
                }
                $builder->expiresAt($this->getExpireAt());
                return $builder;
            }
        );
    }

    public function parser(string $token): UnencryptedToken
    {
        return $this->getJwtFacade()
            ->parse(
                $token,
                new SignedWith(
                    $this->getSigner(),
                    $this->getSigningKey()
                ),
                new StrictValidAt($this->getClock()),
                $this->getBlackListConstraint()
            );
    }

    public function addBlackList(UnencryptedToken $token): bool
    {
        return $this->getCacheDriver()->set($token->toString(), $this->getBlackConfig('ttl', 600));
    }

    public function hasBlackList(UnencryptedToken $token): bool
    {
        return $this->getCacheDriver()->has($token->toString());
    }

    public function removeBlackList(UnencryptedToken $token): bool
    {
        return $this->getCacheDriver()->delete($token->toString());
    }

    public function getConfig(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }

    protected function getJwtFacade(): JwtFacade
    {
        return new JwtFacade(clock: $this->getClock());
    }

    protected function getClock(): Clock
    {
        return new class implements Clock {
            public function now(): \DateTimeImmutable
            {
                return Carbon::now()->toDateTimeImmutable();
            }
        };
    }

    protected function getSigner(): Signer
    {
        return Arr::get($this->config, 'alg');
    }

    protected function getSigningKey(): Key
    {
        return Arr::get($this->config, 'key');
    }

    protected function getCacheDriver(): DriverInterface
    {
        return $this->cacheManager->getDriver($this->getBlackConfig('connection'));
    }

    protected function getBlackConfig(string $name, mixed $default = null): mixed
    {
        return Arr::get($this->config, 'blacklist.' . $name, $default);
    }

    protected function getBlackListConstraint(): Constraint
    {
        $cache = $this->getCacheDriver();
        $enable = $this->getBlackConfig('enable', false);
        return new class($cache, $enable) implements Constraint {
            public function __construct(
                private readonly DriverInterface $driver,
                private readonly bool $enable
            ) {}

            public function assert(Token $token): void
            {
                if (! $this->enable) {
                    return;
                }
                if ($this->driver->has($token->toString())) {
                    throw new ConstraintViolation('Token is blacklisted');
                }
            }
        };
    }

    protected function getExpireAt(): \DateTimeImmutable
    {
        return Carbon::now()->addSeconds(Arr::get($this->config, 'ttl', 600))->toDateTimeImmutable();
    }
}
