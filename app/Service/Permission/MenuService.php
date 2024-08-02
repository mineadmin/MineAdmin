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

namespace App\Service\Permission;

use App\Repository\Permission\MenuRepository;
use App\Service\IService;

class MenuService extends IService
{
    public function __construct(
        protected readonly MenuRepository $repository
    ) {}

    public function getRepository(): MenuRepository
    {
        return $this->repository;
    }
}
