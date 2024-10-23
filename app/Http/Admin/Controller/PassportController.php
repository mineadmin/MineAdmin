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

use App\Http\Admin\Request\PassportLoginRequest;
use App\Http\Admin\Vo\PassportLoginVo;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\RefreshTokenMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Model\Enums\User\Type;
use App\Schema\UserSchema;
use App\Service\PassportService;
use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation as OA;
use Hyperf\Swagger\Annotation\Post;
use Mine\Jwt\Traits\RequestScopedTokenTrait;
use Mine\Swagger\Attributes\ResultResponse;

#[OA\HyperfServer(name: 'http')]
final class PassportController extends AbstractController
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly PassportService $passportService,
        private readonly CurrentUser $currentUser
    ) {}

    #[Post(
        path: '/admin/passport/login',
        operationId: 'passportLogin',
        summary: '系统登录',
        tags: ['admin:passport']
    )]
    #[ResultResponse(
        instance: new Result(data: new PassportLoginVo()),
        title: '登录成功',
        description: '登录成功返回对象',
        example: '{"code":200,"message":"成功","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","refresh_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","expire_at":300}}'
    )]
    #[OA\RequestBody(content: new OA\JsonContent(
        ref: PassportLoginRequest::class,
        title: '登录请求参数',
        required: ['username', 'password'],
        example: '{"username":"admin","password":"123456"}'
    ))]
    public function login(PassportLoginRequest $request): Result
    {
        $username = (string) $request->input('username');
        $password = (string) $request->input('password');
        $browser = $request->header('User-Agent') ?: 'unknown';
        $os = $request->os();
        return $this->success(
            $this->passportService->login(
                $username,
                $password,
                Type::SYSTEM,
                $request->ip(),
                $browser,
                $os
            )
        );
    }

    #[Post(
        path: '/admin/passport/logout',
        operationId: 'passportLogout',
        summary: '退出',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[ResultResponse(instance: new Result(), example: '{"code":200,"message":"成功","data":[]}')]
    #[Middleware(AccessTokenMiddleware::class)]
    public function logout(RequestInterface $request): Result
    {
        $this->passportService->logout($this->getToken());
        return $this->success();
    }

    #[OA\Get(
        path: '/admin/passport/getInfo',
        operationId: 'getInfo',
        summary: '获取用户信息',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(AccessTokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: UserSchema::class),
    )]
    public function getInfo(): Result
    {
        return $this->success(
            Arr::only(
                $this->currentUser->user()?->toArray() ?: [],
                ['username', 'nickname', 'avatar', 'signed', 'backend_setting', 'phone', 'email']
            )
        );
    }

    #[Post(
        path: '/admin/passport/refresh',
        operationId: 'refresh',
        summary: '刷新token',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(RefreshTokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: new PassportLoginVo())
    )]
    public function refresh(CurrentUser $user): Result
    {
        return $this->success($user->refresh());
    }
}
