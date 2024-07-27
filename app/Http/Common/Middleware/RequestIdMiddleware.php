<?php

namespace App\Http\Common\Middleware;

use App\Kernel\Log\UuidRequestIdProcessor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestIdMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request)->setHeader('Request-Id',UuidRequestIdProcessor::getUuid());
    }

}