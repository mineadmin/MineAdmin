<?php

namespace App\Exception;

use App\Kernel\Http\Response;
use App\Kernel\Http\ResultCode;
use Throwable;

class BusinessException extends \RuntimeException
{
    private Response $response;

    public function __construct(string $message = "", ResultCode $code = ResultCode::FAIL, mixed $data = [])
    {
        $this->response = new Response($code,$message,$data);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}