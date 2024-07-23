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

namespace App\Service\Settings;

use App\Repository\Settings\ConfigGroupRepository;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;

class ConfigGroupService extends AbstractService
{
    /**
     * @var ConfigGroupRepository
     */
    public $repository;

    /**
     * ConfigGroupRepository constructor.
     */
    public function __construct(ConfigGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 删除配置组和其所属配置.
     */
    #[Transaction]
    public function deleteConfigGroup(mixed $id): bool
    {
        return $this->repository->deleteGroupAndConfig($id);
    }
}
