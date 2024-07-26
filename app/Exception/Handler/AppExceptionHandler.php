<?php

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Hyperf\Codec\Json;
use Hyperf\Context\ResponseContext;
use Hyperf\ExceptionHandler\ExceptionHandler as Base;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use function App\Kernel\set_cors_response;

class AppExceptionHandler extends Base
{
    /**
     * @param BusinessException $throwable
     * @param ResponsePlusInterface $response
     * @return ResponsePlusInterface
     */
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        $result = new Result();
        switch ($throwable){
            case $throwable instanceof ValidationException:
                $result->code = ResultCode::UNPROCESSABLE_ENTITY;
                $result->message = $throwable->validator->errors()->first();
                break;
            case $throwable instanceof BusinessException:
                $result = $throwable->getResponse();
                break;
            default:
                $result->code = ResultCode::FAIL;
                $result->message = $throwable->getMessage();
        }
        return  $response
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(Json::encode($result)))
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization');
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

}