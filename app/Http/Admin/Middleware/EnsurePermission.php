<?php

namespace App\Http\Admin\Middleware;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        /** @var User $user */
        $user = $request->user('api');

        if ($user->status->isDisable()) {
            throw new BusinessException(ResultCode::Disabled, trans('user.disable'));
        }

        if (! $user->isSuperAdmin() && ! $user->hasPermission($permission)) {
            throw new BusinessException(ResultCode::Forbidden, 'Forbidden');
        }

        return $next($request);
    }
}
