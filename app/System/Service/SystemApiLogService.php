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

use App\System\Mapper\SystemApiLogMapper;
use Mine\Abstracts\AbstractService;

/**
 * api日志业务
 * Class SystemAppService.
 */
class SystemApiLogService extends AbstractService
{
    /**
     * @var SystemApiLogMapper
     */
    public $mapper;

    public function __construct(SystemApiLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
