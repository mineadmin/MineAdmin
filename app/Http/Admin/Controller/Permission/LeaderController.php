<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\LeaderRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Service\Permission\LeaderService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('部门领导管理', '部门领导增删查')]
class LeaderController extends AbstractController
{
    public function __construct(
        private readonly LeaderService $leaderService,
    ) {}

    #[Endpoint('leaderList', '部门领导列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function pageList(Request $request): JsonResponse
    {
        return Result::success([
            'list' => $this->leaderService->getList($request->all()),
        ]);
    }

    #[Endpoint('leaderCreate', '创建部门领导')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function create(LeaderRequest $request): JsonResponse
    {
        $this->leaderService->create($request->validated());

        return Result::success();
    }

    #[Endpoint('leaderDelete', '删除部门领导')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->leaderService->deleteByDoubleKey($request->all());

        return Result::success();
    }
}
