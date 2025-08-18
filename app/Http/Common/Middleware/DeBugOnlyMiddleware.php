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

use Hyperf\Server\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeBugOnlyMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response.
     * @throws ServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! $this->isDebug()) {
            throw new ServerException('This interface can only be accessed in debug mode.');
        }
        return $handler->handle($request);
    }

    /**
     * Check if the application is in debug mode.
     */
    private function isDebug(): bool
    {
        return config('debug', false) === true;
    }
}
