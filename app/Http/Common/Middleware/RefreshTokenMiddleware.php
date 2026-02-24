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

use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Hyperf\Codec\Json;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Mine\Jwt\JwtInterface;
use Mine\JwtAuth\Middleware\AbstractTokenMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swow\Psr7\Message\ServerRequestPlusInterface;

class RefreshTokenMiddleware extends AbstractTokenMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $token = $this->parserToken($request);
        } catch (RequiredConstraintsViolated $e) {
            $isExpired = str_contains($e->getMessage(), 'The token is expired');
            $result = new Result(
                code: ResultCode::UNAUTHORIZED,
                message: $isExpired ? trans('jwt.expired') : trans('jwt.unauthorized'),
            );
            return $this->buildErrorResponse($request, $result);
        }

        $this->checkToken->checkJwt($token);
        return $handler->handle(
            value(
                static function (ServerRequestPlusInterface $request, UnencryptedToken $token) {
                    return $request->setAttribute('token', $token);
                },
                $request,
                $token
            )
        );
    }

    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get();
    }

    protected function parserToken(ServerRequestInterface $request): UnencryptedToken
    {
        return $this->getJwt()->parserRefreshToken($this->getToken($request));
    }

    private function buildErrorResponse(ServerRequestInterface $request, Result $result): ResponseInterface
    {
        /** @var \Swow\Psr7\Message\ResponsePlusInterface $response */
        $response = \Hyperf\Context\Context::get(ResponseInterface::class);
        return $response
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream(Json::encode($result->toArray())));
    }
}