<?php
declare(strict_types=1);

namespace Mine\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\MethodNotAllowedHttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class DataNotFoundExceptionHandler
 * @package Mine\Exception\Handler
 */
class MethodNotAllowExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();
        $format = [
            'success' => false,
            'code'    => MineCode::METHOD_NOT_ALLOW,
            'message' => $throwable->getMessage()
        ];
        return $response->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(Json::encode($format)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof MethodNotAllowedHttpException;
    }
}
