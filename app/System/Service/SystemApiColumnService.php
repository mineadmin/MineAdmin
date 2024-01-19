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

use App\System\Mapper\SystemApiColumnMapper;
use Mine\Abstracts\AbstractService;

/**
 * api接口字段业务
 * Class SystemApiColumnService.
 */
class SystemApiColumnService extends AbstractService
{
    /**
     * @var SystemApiColumnMapper
     */
    public $mapper;

    public function __construct(SystemApiColumnMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
