<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Service\Permission\UserOperationLogService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('用户操作日志', '用户操作日志列表和删除')]
class UserOperationLogController extends AbstractController
{
    public function __construct(
        private readonly UserOperationLogService $userOperationLogService,
    ) {}

    #[Endpoint('userOperationLogList', '用户操作日志列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    #[Response(type: 'array{code: int, message: string, data: array{list: array<int, \App\Models\UserOperationLog>, total: int}}')]
    public function page(Request $request): JsonResponse
    {
        return Result::success($this->userOperationLogService->page(
            $request->all(),
            (int) $request->input('page', 1),
            (int) $request->input('page_size', 10),
        ));
    }

    #[Endpoint('userOperationLogDelete', '删除用户操作日志')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->userOperationLogService->deleteById(Arr::wrap($request->all()));

        return Result::success();
    }
}
