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

namespace App\Service\Logs;

use App\Repository\Logs\LoginLogRepository;
use Mine\Abstracts\AbstractService;

/**
 * 登录日志业务
 * Class LoginLogService.
 */
class LoginLogService extends AbstractService
{
    /**
     * @var LoginLogRepository
     */
    public $repository;

    public function __construct(LoginLogRepository $repository)
    {
        $this->repository = $repository;
    }
}
