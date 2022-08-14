<?php

declare(strict_types=1);
namespace App\System\Controller\Permission;

use App\System\Request\User\SystemUserCreateRequest;
use App\System\Request\User\SystemUserHompPageRequest;
use App\System\Request\User\SystemUserPasswordRequest;
use App\System\Request\User\SystemUserStatusRequest;
use App\System\Request\User\SystemUserUpdateRequest;
use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineCollection;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserController
 * @package App\System\Controller
 */
#[Controller(prefix: "system/user"), Auth]
class UserController extends MineController
{
    #[Inject]
    protected SystemUserService $service;

    /**
     * 用户列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("system:user:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all(), false));
    }

    /**
     * 回收站列表
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("recycle"), Permission("system:user:recycle")]
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 新增一个用户
     * @param SystemUserCreateRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("system:user:save"), OperationLog]
    public function save(SystemUserCreateRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 获取一个用户信息
     * @param int $id
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Permission("system:user:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 更新一个用户信息
     * @param int $id
     * @param SystemUserUpdateRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("system:user:update"), OperationLog]
    public function update(int $id, SystemUserUpdateRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除用户到回收站
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("delete"), Permission("system:user:delete")]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除用户 （清空回收站）
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("realDelete"), Permission("system:user:realDelete"), OperationLog]
    public function realDelete(): ResponseInterface
    {
        return $this->service->realDelete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的用户
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("recovery"), Permission("system:user:recovery"), OperationLog]
    public function recovery(): ResponseInterface
    {
        return $this->service->recovery((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更改用户状态
     * @param SystemUserStatusRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("changeStatus"), Permission("system:user:changeStatus"), OperationLog]
    public function changeStatus(SystemUserStatusRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->success() : $this->error();
    }

    /**
     * 清除用户缓存
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("clearCache"), Permission("system:user:cache")]
    public function clearCache(): ResponseInterface
    {
        $this->service->clearCache((string) $this->request->input('id', null));
        return $this->success();
    }

    /**
     * 设置用户首页
     * @param SystemUserHompPageRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("setHomePage"), Permission("system:user:homePage")]
    public function setHomePage(SystemUserHompPageRequest $request): ResponseInterface
    {
        return $this->service->setHomePage($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 初始化用户密码
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("initUserPassword"), Permission("system:user:initUserPassword"), OperationLog]
    public function initUserPassword(): ResponseInterface
    {
        return $this->service->initUserPassword((int) $this->request->input('id')) ? $this->success() : $this->error();
    }

    /**
     * 更改用户资料，含修改头像 (不验证权限)
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("updateInfo")]
    public function updateInfo(): ResponseInterface
    {
        return $this->service->updateInfo($this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 修改密码 (不验证权限)
     * @param SystemUserPasswordRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("modifyPassword")]
    public function modifyPassword(SystemUserPasswordRequest $request): ResponseInterface
    {
        return $this->service->modifyPassword($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 用户导出
     * @return ResponseInterface
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("export"), Permission("system:user:export"), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), \App\System\Dto\UserDto::class, '用户列表');
    }

    /**
     * 用户导入
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    #[PostMapping("import"), Permission("system:user:import")]
    public function import(): ResponseInterface
    {
        return $this->service->import(\App\System\Dto\UserDto::class) ? $this->success() : $this->error();
    }

    /**
     * 下载导入模板
     * @return ResponseInterface
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("downloadTemplate")]
    public function downloadTemplate(): ResponseInterface
    {
        return (new MineCollection)->export(\App\System\Dto\UserDto::class, '模板下载', []);
    }
}