<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Service\Permission\UserLoginLogService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('用户登录日志', '用户登录日志列表和删除')]
class UserLoginLogController extends AbstractController
{
    public function __construct(
        private readonly UserLoginLogService $userLoginLogService,
    ) {}

    #[Endpoint('userLoginLogList', '用户登录日志列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    #[Response(type: 'array{code: int, message: string, data: array{list: array<int, \App\Models\UserLoginLog>, total: int}}')]
    public function page(Request $request): JsonResponse
    {
        return Result::success($this->userLoginLogService->page(
            $request->all(),
            (int) $request->input('page', 1),
            (int) $request->input('page_size', 10),
        ));
    }

    #[Endpoint('userLoginLogDelete', '删除用户登录日志')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->userLoginLogService->deleteById(Arr::wrap($request->all()));

        return Result::success();
    }
}
