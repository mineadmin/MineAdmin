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

namespace App\Service;

use App\Events\User\LoginSuccessEvent;
use App\Exception\BusinessException;
use App\Exception\JwtInBlackException;
use App\Http\Common\ResultCode;
use App\Kernel\Auth\JwtFactory;
use App\Kernel\Auth\JwtInterface;
use App\Repository\Permission\UserRepository;
use Hyperf\Collection\Arr;
use Lcobucci\JWT\UnencryptedToken;
use Psr\EventDispatcher\EventDispatcherInterface;

final class PassportService extends IService
{
    /**
     * @var string jwt场景
     */
    private string $jwt = 'default';

    public function __construct(
        protected readonly UserRepository $repository,
        protected readonly JwtFactory $jwtFactory,
        protected readonly EventDispatcherInterface $dispatcher
    ) {}

    /**
     * @return array<string,int|string>
     */
    public function login(string $username, string $password): array
    {
        $user = $this->repository->checkUserByUsername($username);
        if (! $this->repository->checkPass($password, $user->password)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, trans('auth.password_error'));
        }
        $this->dispatcher->dispatch(new LoginSuccessEvent($user));
        $jwt = $this->getJwt();
        $token = $jwt->builder($user->only(['id']));
        return [
            'token' => $token->toString(),
            'expire_at' => (int) $jwt->getConfig('ttl', 0),
        ];
    }

    public function checkJwt(UnencryptedToken $token): bool
    {
        $jwt = $this->getJwt();
        if ($jwt->hasBlackList($token)) {
            throw new JwtInBlackException();
        }
        return true;
    }

    public function logout(UnencryptedToken $token)
    {
        $this->getJwt()->addBlackList($token);
    }

    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get($this->jwt);
    }

    /**
     * @return array<string,int|string>
     */
    public function refreshToken(UnencryptedToken $token): array
    {
        $jwt = $this->getJwt();
        $jwt->addBlackList($token);
        $token = $jwt->builder(Arr::only($token->claims()->all(), 'id'));
        return [
            'token' => $token->toString(),
            'expire_at' => (int) $jwt->getConfig('ttl', 0),
        ];
    }
}
