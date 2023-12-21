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

use App\System\Mapper\SystemDictTypeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 字典类型业务
 * Class SystemLoginLogService.
 */
class SystemDictTypeService extends AbstractService
{
    /**
     * @var SystemDictTypeMapper
     */
    public $mapper;

    public function __construct(SystemDictTypeMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
