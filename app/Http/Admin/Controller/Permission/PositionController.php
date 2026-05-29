<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\BatchGrantDataPermissionForPositionRequest;
use App\Http\Admin\Request\PositionRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Service\Permission\PositionService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('岗位管理', '岗位增删改查和数据权限分配')]
class PositionController extends AbstractController
{
    public function __construct(
        private readonly PositionService $positionService,
    ) {}

    #[Endpoint('positionList', '岗位列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function pageList(Request $request): JsonResponse
    {
        return Result::success([
            'list' => $this->positionService->getList($request->all()),
        ]);
    }

    #[Endpoint('positionCreate', '创建岗位')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function create(PositionRequest $request): JsonResponse
    {
        $this->positionService->create($request->validated());

        return Result::success();
    }

    #[Endpoint('positionSave', '保存岗位')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function save(int $id, PositionRequest $request): JsonResponse
    {
        $this->positionService->updateById($id, $request->validated());

        return Result::success();
    }

    #[Endpoint('positionDelete', '删除岗位')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->positionService->deleteById(Arr::wrap($request->all()));

        return Result::success();
    }

    #[Endpoint('positionDataPermission', '设置岗位数据权限')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function batchDataPermission(int $id, BatchGrantDataPermissionForPositionRequest $request): JsonResponse
    {
        $this->positionService->batchDataPermission($id, $request->validated());

        return Result::success();
    }
}
