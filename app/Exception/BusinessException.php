<?php

namespace App\Exception;

use App\Http\Common\Result;
use App\Http\Common\ResultCode;

class BusinessException extends \RuntimeException
{
    private Result $response;

    public function __construct(ResultCode $code = ResultCode::FAIL,?string $message = null, mixed $data = [])
    {
        $this->response = new Result($code,$message,$data);
    }

    /**
     * @return Result
     */
    public function getResponse(): Result
    {
        return $this->response;
    }
}