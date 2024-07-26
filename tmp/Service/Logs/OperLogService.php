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

namespace App\Service\Logs;

use App\Repository\Logs\OperLogRepository;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Interfaces\ServiceInterface\OperLogServiceInterface;

#[DependProxy(values: [OperLogServiceInterface::class])]
class OperLogService extends AbstractService implements OperLogServiceInterface
{
    public $repository;

    public function __construct(OperLogRepository $repository)
    {
        $this->repository = $repository;
    }
}
