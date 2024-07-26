<?php

namespace App\Http\Common\Controller;

use App\Http\Common\Result;
use App\Http\Common\ResultCode;

abstract class AbstractController
{

    protected function success(mixed $data = [],?string $message = null): Result
    {
        return new Result(ResultCode::SUCCESS,$message,$data);
    }

    protected function fail(mixed $data = [],?string $message = null): Result
    {
        return new Result(ResultCode::FAIL,$message,$data);
    }

    protected function json(ResultCode $code,mixed $data = [],?string $message = null): Result
    {
        return new Result($code,$message,$data);
    }
}