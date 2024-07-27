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

use App\Kernel\Auth\JwtFactory;
use App\Kernel\Auth\JwtInterface;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtFactory $jwtFactory
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle(
            $request->setAttribute(
                'token',
                $this->getJwt()->parser($this->getToken($request))
            )
        );
    }

    private function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get();
    }

    private function getToken(ServerRequestInterface $request): string
    {
        if ($request->hasHeader('Authorization')) {
            return Str::replace('Bear ', '', $request->getHeaderLine('Authorization'));
        }
        if ($request->hasHeader('token')) {
            return $request->getHeaderLine('token');
        }
        if (Arr::has($request->getQueryParams(), 'token')) {
            return $request->getQueryParams()['token'];
        }
        return '';
    }
}
