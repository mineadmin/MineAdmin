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

use App\System\Mapper\SystemUserMapper;
use App\System\Model\SystemUser;
use Hyperf\Database\Model\ModelNotFoundException;
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
class UserAuthService implements UserServiceInterface
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
        $mapper = container()->get(SystemUserMapper::class);
        try {
            event(new UserLoginBefore(['username' => $userServiceVo->getUsername(), 'password' => $userServiceVo->getPassword()]));
            $userinfo = $mapper->checkUserByUsername($userServiceVo->getUsername())->toArray();
            $password = $userinfo['password'];
            unset($userinfo['password']);
            $userLoginAfter = new UserLoginAfter($userinfo);
            if ($mapper->checkPass($userServiceVo->getPassword(), $password)) {
                if (
                    ($userinfo['status'] == SystemUser::USER_NORMAL)
                    ||
                    ($userinfo['status'] == SystemUser::USER_BAN && $userinfo['id'] == \Hyperf\Support\env('SUPER_ADMIN'))
                ) {
                    $userLoginAfter->message = t('jwt.login_success');
                    $token = user()->getToken($userLoginAfter->userinfo);
                    $userLoginAfter->token = $token;
                    event($userLoginAfter);
                    return $token;
                } else {
                    $userLoginAfter->loginStatus = false;
                    $userLoginAfter->message = t('jwt.user_ban');
                    event($userLoginAfter);
                    throw new UserBanException;
                }
            } else {
                $userLoginAfter->loginStatus = false;
                $userLoginAfter->message = t('jwt.password_error');
                event($userLoginAfter);
                throw new NormalStatusException;
            }
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw new NormalStatusException(t('jwt.username_error'), MineCode::NO_DATA);
            }
            if ($e instanceof NormalStatusException) {
                throw new NormalStatusException(t('jwt.password_error'), MineCode::PASSWORD_ERROR);
            }
            if ($e instanceof UserBanException) {
                throw new NormalStatusException(t('jwt.user_ban'), MineCode::USER_BAN);
            }
            console()->error($e->getMessage());
            throw new NormalStatusException(t('jwt.unknown_error'));
        }
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