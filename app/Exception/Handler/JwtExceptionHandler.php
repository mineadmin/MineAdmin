<?php

namespace App\Exception\Handler;

use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Lcobucci\JWT\Exception;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class JwtExceptionHandler extends AbstractHandler
{
    function handleResponse(Throwable $throwable): Result
    {
        $this->stopPropagation();
        switch ($throwable){
            default:
                return new Result(
                    code: ResultCode::UNAUTHORIZED,
                    message: trans('jwt.unauthorized')
                );
        }
    }


    public function isValid(Throwable $throwable): bool
    {
        return ($throwable instanceof Exception);
    }

}