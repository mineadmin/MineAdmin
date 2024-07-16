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

use App\Mapper\System\DictTypeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 字典类型业务
 * Class LoginLogService.
 */
class DictTypeService extends AbstractService
{
    /**
     * @var DictTypeMapper
     */
    public $mapper;

    public function __construct(DictTypeMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
