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

namespace Mine\JwtAuth\Middleware;

use App\Service\PassportService;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Mine\Jwt\Factory;
use Mine\Jwt\JwtInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swow\Psr7\Message\ServerRequestPlusInterface;

abstract class AbstractTokenMiddleware
{
    public function __construct(
        protected readonly Factory $jwtFactory,
        protected readonly PassportService $userService
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->userService->checkJwt($this->parserToken($request));
        return $handler->handle(
            value(
                static function (ServerRequestPlusInterface $request, UnencryptedToken $token) {
                    return $request->setAttribute('token', $token);
                },
                $request,
                $this->getJwt()->parserAccessToken(
                    $this->getToken($request)
                )
            )
        );
    }

    abstract public function getJwt(): JwtInterface;

    protected function parserToken(ServerRequestInterface $request): Token
    {
        return $this->getJwt()->parserAccessToken($this->getToken($request));
    }

    protected function getToken(ServerRequestInterface $request): string
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
