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

namespace App\System\Service;

use App\System\Mapper\SystemLoginLogMapper;
use Mine\Abstracts\AbstractService;

/**
 * 登录日志业务
 * Class SystemLoginLogService.
 */
class SystemLoginLogService extends AbstractService
{
    /**
     * @var SystemLoginLogMapper
     */
    public $mapper;

    public function __construct(SystemLoginLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
