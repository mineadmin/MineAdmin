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

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

class RefreshTokenConstraint implements Constraint
{
    public function assert(Token $token): void
    {
        if ($token->isRelatedTo('refresh')) {
            throw ConstraintViolation::error('Token is a refresh token', $this);
        }
    }
}
