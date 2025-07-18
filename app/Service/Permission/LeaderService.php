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

use App\Model\Permission\Leader;
use App\Repository\Permission\LeaderRepository;
use App\Service\IService;
use Mockery\Exception;

/**
 * @extends IService<Leader>
 */
class LeaderService extends IService
{
    public function __construct(
        protected readonly LeaderRepository $repository
    ) {}

    public function deleteByDoubleKey(array $data): bool
    {
        try {
            $this->repository->deleteByDoubleKey($data['dept_id'], $data['user_ids']);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
