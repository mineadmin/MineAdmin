<?php

namespace App\Exception\Handler;

use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Hyperf\Validation\ValidationException;
use Throwable;

class ValidationExceptionHandler extends AbstractHandler
{
    /**
     * @param ValidationException $throwable
     * @return Result
     */
    function handleResponse(\Throwable $throwable): Result
    {
        $this->stopPropagation();
        return new Result(
            code:ResultCode::UNPROCESSABLE_ENTITY,
            message: $throwable->validator->errors()->first()
        );
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}