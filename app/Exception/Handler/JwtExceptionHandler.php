<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Lcobucci\JWT\Exception;

final class JwtExceptionHandler extends AbstractHandler
{
    public function handleResponse(\Throwable $throwable): Result
    {
        $this->stopPropagation();
        return match (true) {
            $throwable->getMessage() === 'The token is expired' => new Result(
                code: ResultCode::UNAUTHORIZED,
                message: trans('jwt.expired'),
            ),
            default => new Result(
                code: ResultCode::UNAUTHORIZED,
                message: trans('jwt.unauthorized'),
                data: [
                    'error' => $throwable->getMessage(),
                ]
            ),
        };
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof Exception;
    }
}
