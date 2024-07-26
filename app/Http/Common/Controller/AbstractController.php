<?php

namespace App\Http\Common\Controller;

use App\Kernel\Http\Response;
use App\Kernel\Http\ResultCode;

abstract class AbstractController
{
    protected function success(mixed $data = [],?string $message = null): Response
    {
        return new Response(ResultCode::SUCCESS,$message,$data);
    }

    protected function error(?string $message = null,mixed $data = []): Response
    {
        return new Response(ResultCode::FAIL,$message,$data);
    }

    protected function json(ResultCode $code,mixed $data = [],?string $message = null): Response
    {
        return new Response($code,$message,$data);
    }
}