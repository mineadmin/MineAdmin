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

use Hyperf\Cache\Driver\DriverInterface;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

class BlackListConstraint implements Constraint
{
    public function __construct(
        private readonly bool $enable,
        private readonly DriverInterface $cache
    ) {}

    public function assert(Token $token): void
    {
        if ($this->enable !== true) {
            return;
        }
        if ($this->cache->has($token->toString())) {
            throw ConstraintViolation::error('Token is in blacklist', $this);
        }
    }
}
