<?php

namespace App\Http\Admin\Middleware;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccessToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken() !== null) {
            auth('api')->forgetUser();
            auth('api')->setToken($request->bearerToken());
        }

        if (auth('api')->getPayload()->get('token_type') !== 'access') {
            auth('api')->forgetUser();
            auth('api')->unsetToken();

            throw new BusinessException(ResultCode::Unauthorized, trans('auth.unauthenticated'));
        }

        return $next($request);
    }
}
