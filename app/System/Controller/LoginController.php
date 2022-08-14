<?php

declare(strict_types=1);
namespace App\System\Controller;

use App\System\Request\User\SystemUserLoginRequest;
use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Snowflake\IdGeneratorInterface;
use Mine\Annotation\Auth;
use Mine\Helper\LoginUser;
use Mine\Interfaces\UserServiceInterface;
use Mine\MineController;
use Mine\Vo\UserServiceVo;
use Psr\Http\Message\ResponseInterface;
use Swoole\Coroutine\Channel;

/**
 * Class LoginController
 * @package App\System\Controller
 */
#[Controller(prefix: "system")]
class LoginController extends MineController
{
    #[Inject]
    protected SystemUserService $systemUserService;

    #[Inject]
    protected UserServiceInterface $userService;

    /**
     * @param SystemUserLoginRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[PostMapping("login")]
    public function login(SystemUserLoginRequest $request): ResponseInterface
    {
        $requestData = $request->validated();
        $vo = new UserServiceVo();
        $vo->setUsername($requestData['username']);
        $vo->setPassword($requestData['password']);
        $vo->setVerifyCode($requestData['code']);
        return $this->success(['token' => $this->userService->login($vo)]);
    }

    /**
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[PostMapping("logout"), Auth]
    public function logout(): ResponseInterface
    {
        $this->userService->logout();
        return $this->success();
    }

    /**
     * 用户信息
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getInfo"), Auth]
    public function getInfo(): ResponseInterface
    {
        return $this->success($this->systemUserService->getInfo());
    }

    /**
     * 刷新token
     * @param LoginUser $user
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[PostMapping("refresh")]
    public function refresh(LoginUser $user): ResponseInterface
    {
        return $this->success(['token' => $user->refresh()]);
    }
}