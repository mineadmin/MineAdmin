<?php

declare(strict_types=1);
namespace Mine\Middlewares;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Hyperf\Utils\Codec\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpCoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * Handle the response when cannot found any routes.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function handleNotFound(ServerRequestInterface $request): ResponseInterface
    {
        $format = [
            'success' => false,
            'code'    => MineCode::NOT_FOUND,
            'message' => t('mineadmin.not_found')
        ];
        return $this->response()->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(404)
            ->withBody(new SwooleStream(Json::encode($format)));
    }

    /**
     * Handle the response when the routes found but doesn't match any available methods.
     * @param array $methods
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function handleMethodNotAllowed(
        array $methods,
        ServerRequestInterface $request): ResponseInterface
    {
        $format = [
            'success' => false,
            'code'    => MineCode::METHOD_NOT_ALLOW,
            'message' => t('mineadmin.allow_method', ['method' => implode(',', $methods)])
        ];
        return $this->response()->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(405)
            ->withBody(new SwooleStream(Json::encode($format)));
    }
}