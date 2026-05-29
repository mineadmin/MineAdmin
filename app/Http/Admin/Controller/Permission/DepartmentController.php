<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\DepartmentRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Service\Permission\DepartmentService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('部门管理', '部门增删改查')]
class DepartmentController extends AbstractController
{
    public function __construct(
        private readonly DepartmentService $departmentService,
    ) {}

    #[Endpoint('departmentList', '部门列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function pageList(Request $request): JsonResponse
    {
        return Result::success([
            'list' => $this->departmentService->getList($request->all()),
        ]);
    }

    #[Endpoint('departmentCreate', '创建部门')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function create(DepartmentRequest $request): JsonResponse
    {
        $this->departmentService->create($request->validated());

        return Result::success();
    }

    #[Endpoint('departmentSave', '保存部门')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function save(int $id, DepartmentRequest $request): JsonResponse
    {
        $this->departmentService->updateById($id, $request->validated());

        return Result::success();
    }

    #[Endpoint('departmentDelete', '删除部门')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->departmentService->deleteById(Arr::wrap($request->all()));

        return Result::success();
    }
}
