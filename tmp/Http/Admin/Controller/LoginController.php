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

namespace App\Http\Admin\Controller;

use App\Http\Admin\Request\UserRequest;
use App\Service\Permission\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Helper\LoginUser;
use Mine\Interfaces\UserServiceInterface;
use Mine\MineController;
use Mine\Vo\UserServiceVo;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LoginController.
 */
#[Controller(prefix: 'system')]
class LoginController extends MineController
{
    #[Inject]
    protected UserService $systemUserService;

    #[Inject]
    protected UserServiceInterface $userService;

    /**
     * 登录.
     */
    #[PostMapping('login')]
    public function login(UserRequest $request): ResponseInterface
    {
        $requestData = $request->validated();
        $vo = new UserServiceVo();
        $vo->setUsername($requestData['username']);
        $vo->setPassword($requestData['password']);
        return $this->success(['token' => $this->userService->login($vo)]);
    }

    /**
     * 退出.
     */
    #[PostMapping('logout'), Auth]
    public function logout(): ResponseInterface
    {
        $this->userService->logout();
        return $this->success();
    }

    /**
     * 用户信息.
     */
    #[GetMapping('getInfo'), Auth]
    public function getInfo(): ResponseInterface
    {
        return $this->success($this->systemUserService->getInfo());
    }

    /**
     * 刷新token.
     */
    #[PostMapping('refresh')]
    public function refresh(LoginUser $user): ResponseInterface
    {
        return $this->success(['token' => $user->refresh()]);
    }

    /**
     * 获取每日的必应背景图.
     */
    #[GetMapping('getBingBackgroundImage')]
    public function getBingBackgroundImage(): ResponseInterface
    {
        try {
            $response = file_get_contents('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
            $content = json_decode($response);
            if (! empty($content?->images[0]?->url)) {
                return $this->success([
                    'url' => 'https://cn.bing.com' . $content?->images[0]?->url,
                ]);
            }
            throw new \Exception();
        } catch (\Exception $e) {
            return $this->error('获取必应背景失败');
        }
    }
}
