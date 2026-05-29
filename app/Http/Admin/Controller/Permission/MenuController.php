<?php

namespace App\Http\Admin\Controller\Permission;

use App\Http\Admin\Request\MenuRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Models\User;
use App\Service\Permission\MenuService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Group('菜单管理', '菜单增删改查')]
class MenuController extends AbstractController
{
    public function __construct(
        private readonly MenuService $menuService,
    ) {}

    #[Endpoint('menuList', '菜单列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function pageList(): JsonResponse
    {
        return Result::success($this->menuService->list());
    }

    #[Endpoint('menuCreate', '创建菜单')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function create(MenuRequest $request): JsonResponse
    {
        $this->menuService->create(array_merge($request->validated(), [
            'created_by' => $this->user($request)->id,
        ]));

        return Result::success();
    }

    #[Endpoint('menuSave', '保存菜单')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function save(int $id, MenuRequest $request): JsonResponse
    {
        $this->menuService->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->user($request)->id,
        ]));

        return Result::success();
    }

    #[Endpoint('menuDelete', '删除菜单')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(Request $request): JsonResponse
    {
        $this->menuService->deleteById(Arr::wrap($request->all()));

        return Result::success();
    }

    private function user(Request $request): User
    {
        /** @var User $user */
        $user = $request->user('api');

        return $user;
    }
}
