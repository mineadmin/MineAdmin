<?php

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use Hyperf\Codec\Json;
use Hyperf\ExceptionHandler\Annotation\ExceptionHandler;
use Hyperf\ExceptionHandler\ExceptionHandler as Base;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

#[ExceptionHandler]
class BusinessExceptionHandler extends Base
{
    /**
     * @param BusinessException $throwable
     * @param ResponsePlusInterface $response
     * @return ResponsePlusInterface
     */
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        return $response->setBody(new SwooleStream(Json::encode($throwable->getResponse()->toArray())));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }

}