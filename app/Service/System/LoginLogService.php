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

namespace App\Service\System;

use App\Mapper\System\LoginLogMapper;
use Mine\Abstracts\AbstractService;

/**
 * 登录日志业务
 * Class LoginLogService.
 */
class LoginLogService extends AbstractService
{
    /**
     * @var LoginLogMapper
     */
    public $mapper;

    public function __construct(LoginLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
