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

namespace App\Service\Setting;

use App\Mapper\Setting\CrontabLogMapper;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Interfaces\ServiceInterface\CrontabLogServiceInterface;

#[DependProxy(values: [CrontabLogServiceInterface::class])]
class CrontabLogService extends AbstractService implements CrontabLogServiceInterface
{
    /**
     * @var CrontabLogMapper
     */
    public $mapper;

    public function __construct(CrontabLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
