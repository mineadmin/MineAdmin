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

use App\Model\Permission\User;
use App\Repository\Permission\UserRepository;
use App\Service\IService;

/**
 * @extends IService<UserRepository>
 */
final class UserService extends IService
{
    public function __construct(
        protected readonly UserRepository $repository,
    ) {}

    public function getInfo(int $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function getFieldByUserId(int $userId, string $field): mixed
    {
        return $this->repository->getQuery([
            'id' => $userId,
        ])->value($field);
    }

    public function resetPassword(int $id): bool
    {
        $entity = $this->repository->findById($id);
        $entity->resetPassword();
        $entity->save();
        return true;
    }
}
