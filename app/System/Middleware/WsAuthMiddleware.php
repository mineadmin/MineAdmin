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

namespace App\System\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WsAuthMiddleware implements MiddlewareInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getQueryParams()['token'] ?? null;
        try {
            if ($token && user()->check($token)) {
                return $handler->handle($request);
            }
            return container()->get(\Hyperf\HttpServer\Contract\ResponseInterface::class)->raw(t('jwt.validate_fail'));
        } catch (\Exception $e) {
            return container()->get(\Hyperf\HttpServer\Contract\ResponseInterface::class)->raw($e->getMessage());
        }
    }
}
