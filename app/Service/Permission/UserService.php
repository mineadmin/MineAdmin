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

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Kernel\Auth\JwtFactory;
use App\Repository\Permission\UserRepository;
use App\Service\AbstractCrudService;

/**
 * @extends AbstractCrudService<UserRepository>
 */
class UserService extends AbstractCrudService
{
    public function __construct(
        protected readonly UserRepository $repository,
        protected readonly JwtFactory $jwtFactory
    ) {}

    public function login(string $username, string $password): array
    {
        $user = $this->getRepository()->checkUserByUsername($username);
        if (! $this->getRepository()->checkPass($password, $user->password)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, trans('auth.password_error'));
        }
        $jwt = $this->jwtFactory->get();
        $token = $jwt->builder($user->only(['id']));
        return [
            'token' => $token->toString(),
            'expire_at' => (int) $jwt->getConfig('ttl', 0)
        ];
    }
}
