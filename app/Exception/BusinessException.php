<?php

namespace App\Exception;

use App\Http\Common\ResultCode;
use Exception;

class BusinessException extends Exception
{
    public function __construct(public ResultCode $resultCode, string $message, public mixed $data = null)
    {
        parent::__construct($message);
    }
}
