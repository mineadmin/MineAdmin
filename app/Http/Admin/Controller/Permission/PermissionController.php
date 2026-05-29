<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\PermissionRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Models\User;
use App\Service\Permission\UserService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('权限', '当前用户菜单、角色和信息更新')]
class PermissionController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Endpoint('adminPermissionMenus', '获取当前用户菜单')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function menus(Request $request): JsonResponse
    {
        return Result::success($this->userService->menus($this->user($request)));
    }

    #[Endpoint('adminPermissionRoles', '获取当前用户角色')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function roles(Request $request): JsonResponse
    {
        return Result::success($this->userService->roles($this->user($request)));
    }

    #[Endpoint('adminPermissionUpdate', '更新用户信息')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function update(PermissionRequest $request): JsonResponse
    {
        $this->userService->updateProfile($this->user($request), $request->validated());

        return Result::success();
    }

    private function user(Request $request): User
    {
        /** @var User $user */
        $user = $request->user('api');

        return $user;
    }
}
