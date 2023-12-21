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

namespace App\Setting\Service;

use App\Setting\Mapper\SettingCrontabLogMapper;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Interfaces\ServiceInterface\CrontabLogServiceInterface;

#[DependProxy(values: [CrontabLogServiceInterface::class])]
class SettingCrontabLogService extends AbstractService implements CrontabLogServiceInterface
{
    /**
     * @var SettingCrontabLogMapper
     */
    public $mapper;

    public function __construct(SettingCrontabLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
