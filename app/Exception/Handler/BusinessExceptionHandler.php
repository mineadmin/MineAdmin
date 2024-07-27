<?php

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use App\Http\Common\Result;
use Throwable;

class BusinessExceptionHandler extends AbstractHandler
{

    /**
     * @param BusinessException $throwable
     */
    function handleResponse(Throwable $throwable): Result
    {
        $this->stopPropagation();
        return $throwable->getResponse();
    }

    /**
     * @inheritDoc
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}