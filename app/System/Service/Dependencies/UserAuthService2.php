<?php
declare(strict_types=1);

/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */
namespace App\System\Service\Dependencies;

use Mine\Event\UserLoginAfter;
use Mine\Event\UserLoginBefore;
use Mine\Event\UserLogout;
use Mine\Exception\NormalStatusException;
use Mine\Exception\UserBanException;
use Mine\Helper\MineCode;
use Mine\Interfaces\UserServiceInterface;
use Mine\Vo\UserServiceVo;
use Mine\Annotation\DependProxy;
/**
 * 用户登录
 */
#[DependProxy(values: [ UserServiceInterface::class ])]
class UserAuthService2 implements UserServiceInterface
{

    /**
     * 登录
     * @param UserServiceVo $userServiceVo
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(UserServiceVo $userServiceVo): string
    {
        throw new NormalStatusException("啊啦啦啦啦", MineCode::NO_DATA);
    }

    /**
     * 用户退出
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function logout()
    {
        $user = user();
        event(new UserLogout($user->getUserInfo()));
        $user->getJwt()->logout();
    }
}