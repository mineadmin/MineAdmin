<?php

declare(strict_types=1);
namespace App\System\Controller;

use App\System\Service\SystemDeptService;
use App\System\Service\SystemPostService;
use App\System\Service\SystemRoleService;
use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 公共方法控制器
 * Class CommonController
 * @package App\System\Controller
 * @Controller(prefix="system/common")
 * @Auth
 */
class CommonController extends MineController
{
    /**
     * @Inject
     * @var SystemUserService
     */
    protected $userService;

    /**
     * @Inject
     * @var SystemDeptService
     */
    protected $deptService;

    /**
     * @Inject
     * @var SystemRoleService
     */
    protected $roleService;

    /**
     * @Inject
     * @var SystemPostService
     */
    protected $postService;

    /**
     * 获取用户列表
     * @GetMapping("getUserList")
     * @return ResponseInterface
     */
    public function getUserList(): ResponseInterface
    {
        return $this->success($this->userService->getPageList($this->request->all()));
    }

    /**
     * 获取部门树列表
     * @GetMapping("getDeptTreeList")
     * @return ResponseInterface
     */
    public function getDeptTreeList(): ResponseInterface
    {
        return $this->success($this->deptService->getSelectTree());
    }

    /**
     * 获取角色列表
     * @GetMapping("getRoleList")
     * @return ResponseInterface
     */
    public function getRoleList(): ResponseInterface
    {
        return $this->success($this->roleService->getList());
    }

    /**
     * 获取岗位列表
     * @GetMapping("getPostList")
     * @return ResponseInterface
     */
    public function getPostList(): ResponseInterface
    {
        return $this->success($this->postService->getList());
    }
}