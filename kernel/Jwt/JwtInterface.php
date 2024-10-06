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

use Lcobucci\JWT\UnencryptedToken;

interface JwtInterface
{
    public function builderAccessToken(string $sub): UnencryptedToken;

    public function builderRefreshToken(string $sub): UnencryptedToken;

    public function parserAccessToken(string $accessToken): UnencryptedToken;

    public function parserRefreshToken(string $refreshToken): UnencryptedToken;

    public function addBlackList(UnencryptedToken $token): bool;

    public function hasBlackList(UnencryptedToken $token): bool;

    public function removeBlackList(UnencryptedToken $token): bool;

    public function getConfig(string $key, mixed $default = null): mixed;
}
