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
use App\Service\Permission\UserService;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use Lcobucci\JWT\UnencryptedToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swow\Psr7\Message\ServerRequestPlusInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtFactory $jwtFactory,
        private readonly UserService $userService
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->getJwt()->parser($this->getToken($request));
        $this->userService->checkJwt($token);

        return $handler->handle(
            value(
                function (ServerRequestPlusInterface $request, UnencryptedToken $token) {
                    $this->userService->checkJwt($token);
                    return $request->setAttribute('token', $token);
                },
                $request,
                $this->getJwt()->parser(
                    $this->getToken($request)
                )
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
            return Str::replace('Bearer ', '', $request->getHeaderLine('Authorization'));
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
