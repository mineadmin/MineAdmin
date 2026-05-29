<?php

namespace App\Http\Common;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class Result
{
    public static function fail(mixed $data = [], string $message = 'success'): JsonResponse
    {
        return self::json(ResultCode::Failure, $data, $message);
    }

    public static function success(mixed $data = [], string $message = 'success'): JsonResponse
    {
        return self::json(ResultCode::Success, $data, $message);
    }

    public static function json(ResultCode $code, mixed $data = [], string $message = 'success'): JsonResponse
    {
        return response()->json([
            /**
             * 系统响应码
             */
            'code' => $code,
            /**
             * 系统消息
             */
            'message' => $message,
            /**
             * 响应数据
             */
            'data' => $data instanceof JsonResource ? $data->resolve(request()) : $data,
        ]);
    }
}
