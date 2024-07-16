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

use App\Mapper\Setting\ConfigGroupMapper;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;

class ConfigGroupService extends AbstractService
{
    /**
     * @var ConfigGroupMapper
     */
    public $mapper;

    /**
     * SettingConfigGroupMappe constructor.
     */
    public function __construct(ConfigGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 删除配置组和其所属配置.
     */
    #[Transaction]
    public function deleteConfigGroup(mixed $id): bool
    {
        return $this->mapper->deleteGroupAndConfig($id);
    }
}
