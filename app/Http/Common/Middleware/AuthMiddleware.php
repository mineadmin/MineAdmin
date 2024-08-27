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

use Mine\Kernel\Jwt\JwtInterface;
use Mine\Kernel\JwtAuth\Middleware\AbstractAuthMiddleware;
use Psr\Http\Server\MiddlewareInterface;

final class AuthMiddleware extends AbstractAuthMiddleware
{
    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get();
    }
}
