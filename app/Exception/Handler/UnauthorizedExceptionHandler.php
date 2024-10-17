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
use Hyperf\Validation\UnauthorizedException;

final class UnauthorizedExceptionHandler extends AbstractHandler
{
    public function handleResponse(\Throwable $throwable): Result
    {
        return new Result(
            ResultCode::FORBIDDEN,
        );
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof UnauthorizedException;
    }
}
