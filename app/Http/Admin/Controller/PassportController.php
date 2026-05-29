<?php

namespace App\Http\Admin\Controller;

use App\Http\Admin\Request\PassportLoginRequest;
use App\Http\Admin\Resource\UserInfoResource;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Models\Enums\User\Type;
use App\Service\PassportService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('授权', '用户登录、退出、用户信息')]
class PassportController extends AbstractController
{
    public function __construct(
        private readonly PassportService $passportService
    ) {}

    #[Endpoint('adminPassportLogin', '登录', '使用后台系统用户账号密码换取访问令牌和刷新令牌。')]
    public function login(PassportLoginRequest $request): JsonResponse
    {
        $username = (string) $request->input('username');
        $password = (string) $request->input('password');
        $browser = $request->header('User-Agent') ?: 'unknown';
        $os = $request->os();

        return Result::success(
            $this->passportService->login($username, $password, Type::System, $request->ip(), $browser, $os),
        );
    }

    #[Endpoint('adminPassportLogout', '退出')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function logout(Request $request): JsonResponse
    {
        $this->passportService->logout($request->bearerToken());

        return Result::success();
    }

    #[Endpoint('adminPassportGetInfo', '用户信息')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function getInfo(Request $request): JsonResponse
    {
        return Result::success(new UserInfoResource($request->user('api')));
    }

    #[Endpoint('adminPassportRefresh', '刷新后台令牌', '使用刷新令牌换取新的访问令牌和刷新令牌。')]
    #[HeaderParameter('Authorization', 'Bearer 刷新令牌', required: true, type: 'string', example: 'Bearer {refresh_token}')]
    public function refresh(Request $request): JsonResponse
    {
        return Result::success($this->passportService->refresh($request->bearerToken()));
    }
}
