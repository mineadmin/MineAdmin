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

namespace App\Http\Common\Middleware;

use Lcobucci\JWT\Token;
use Mine\Kernel\Jwt\JwtInterface;
use Mine\Kernel\JwtAuth\Middleware\AbstractTokenMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTokenMiddleware extends AbstractTokenMiddleware
{
    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get();
    }

    protected function parserToken(ServerRequestInterface $request): Token
    {
        return $this->getJwt()->parserRefreshToken($this->getToken($request));
    }
}
