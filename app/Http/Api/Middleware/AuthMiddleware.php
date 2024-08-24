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

namespace App\Http\Api\Middleware;

use App\Http\Common\Middleware\AuthMiddleware as CommonAuthMiddleware;
use App\Kernel\Auth\JwtInterface;

final class AuthMiddleware extends CommonAuthMiddleware
{
    protected function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get('api');
    }
}
