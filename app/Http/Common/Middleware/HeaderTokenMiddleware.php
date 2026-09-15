<?php

declare(strict_types=1);

namespace App\Http\Common\Middleware;

use Mine\JwtAuth\Middleware\AbstractTokenMiddleware;
use Psr\Http\Message\ServerRequestInterface;

abstract class HeaderTokenMiddleware extends AbstractTokenMiddleware
{
    protected function getToken(ServerRequestInterface $request): string
    {
        $authorization = trim($request->getHeaderLine('Authorization'));
        if ($authorization !== '' && preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches) === 1) {
            return trim($matches[1]);
        }

        return trim($request->getHeaderLine('token'));
    }
}
