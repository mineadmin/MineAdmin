<?php

namespace App\Service\Permission;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Kernel\Auth\JwtFactory;
use App\Repository\Permission\UserRepository;
use App\Service\AbstractCrudService;
use function Symfony\Component\Translation\t;

/**
 * @extends AbstractCrudService<UserRepository>
 */
class UserService extends AbstractCrudService
{
    public function __construct(
        private UserRepository $repository,
        private JwtFactory $jwtFactory
    ){}

    public function login(string $username,string $password): array
    {
        $user = $this->getRepository()->checkUserByUsername($username);

        if (!$this->getRepository()->checkPass($password,$user->password)){
            throw new BusinessException(ResultCode::UNAUTHORIZED,trans('auth.password_error'));
        }
        $jwt =$this->jwtFactory->get();
        $token = $jwt->builder($user->only(['id']));
        return [
            'token' => $token->toString(),
            'expire_at' => (int)$jwt->getConfig('ttl',0),
            'user'  =>  $user->only([
                'username',
                'nickname',
                'avatar',
                'status',
                'created_at',
            ])
        ];
    }
}