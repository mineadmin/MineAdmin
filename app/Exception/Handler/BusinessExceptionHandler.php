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

use App\Exception\BusinessException;
use App\Http\Common\Result;

final class BusinessExceptionHandler extends AbstractHandler
{
    /**
     * @param BusinessException $throwable
     */
    public function handleResponse(\Throwable $throwable): Result
    {
        $this->stopPropagation();
        return $throwable->getResponse();
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}
