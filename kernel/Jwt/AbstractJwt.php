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

namespace Mine\Jwt;

use Carbon\Carbon;
use Hyperf\Cache\CacheManager;
use Hyperf\Cache\Driver\DriverInterface;
use Hyperf\Collection\Arr;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;

abstract class AbstractJwt implements JwtInterface
{
    public function __construct(
        private readonly array $config,
        private readonly CacheManager $cacheManager,
        private readonly Clock $clock,
        private readonly RefreshTokenConstraint $refreshTokenConstraint
    ) {}

    public function builderRefreshToken(string $sub): UnencryptedToken
    {
        return $this->getJwtFacade()->issue(
            $this->getSigner(),
            $this->getSigningKey(),
            function (Builder $builder, \DateTimeImmutable $immutable) use ($sub) {
                $builder = $builder->identifiedBy($sub);
                $builder = $builder->expiresAt($this->getRefreshExpireAt($immutable));
                return $builder->relatedTo('refresh');
            }
        );
    }

    public function builderAccessToken(string $sub): UnencryptedToken
    {
        return $this->getJwtFacade()->issue(
            $this->getSigner(),
            $this->getSigningKey(),
            function (Builder $builder, \DateTimeImmutable $immutable) use ($sub) {
                $builder = $builder->identifiedBy($sub);
                return $builder->expiresAt($this->getExpireAt($immutable));
            }
        );
    }

    public function parserRefreshToken(string $refreshToken): UnencryptedToken
    {
        return $this->getJwtFacade()
            ->parse(
                $refreshToken,
                new SignedWith(
                    $this->getSigner(),
                    $this->getSigningKey()
                ),
                new StrictValidAt(
                    $this->clock,
                    $this->clock->now()->diff($this->getRefreshExpireAt($this->clock->now()))
                ),
                $this->getBlackListConstraint()
            );
    }

    public function parserAccessToken(string $accessToken): UnencryptedToken
    {
        return $this->getJwtFacade()
            ->parse(
                $accessToken,
                new SignedWith(
                    $this->getSigner(),
                    $this->getSigningKey()
                ),
                new StrictValidAt(
                    $this->clock,
                    $this->clock->now()->diff($this->getExpireAt($this->clock->now()))
                ),
                $this->getBlackListConstraint(),
                $this->refreshTokenConstraint
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

    private function getJwtFacade(): JwtFacade
    {
        return new JwtFacade(clock: $this->clock);
    }

    private function getSigner(): Signer
    {
        return Arr::get($this->config, 'alg');
    }

    private function getSigningKey(): Key
    {
        return Arr::get($this->config, 'key');
    }

    private function getCacheDriver(): DriverInterface
    {
        return $this->cacheManager->getDriver($this->getBlackConfig('connection'));
    }

    private function getBlackConfig(string $name, mixed $default = null): mixed
    {
        return Arr::get($this->config, 'blacklist.' . $name, $default);
    }

    private function getBlackListConstraint(): Constraint
    {
        return new BlackListConstraint((bool) $this->getBlackConfig('enable', false), $this->getCacheDriver());
    }

    private function getExpireAt(\DateTimeImmutable $immutable): \DateTimeImmutable
    {
        return Carbon::create($immutable)
            ->addSeconds(Arr::get($this->config, 'ttl', 3600))
            ->toDateTimeImmutable();
    }

    private function getRefreshExpireAt(\DateTimeImmutable $immutable): \DateTimeImmutable
    {
        return Carbon::create($immutable)
            ->addSeconds(Arr::get($this->config, 'refresh_ttl', 7200))
            ->toDateTimeImmutable();
    }
}
