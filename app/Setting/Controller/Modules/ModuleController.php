<?php

declare(strict_types=1);
namespace App\Setting\Controller\Modules;

use App\Setting\Request\Module\ModuleCreateRequest;
use App\Setting\Service\ModuleService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 本地模块管理
 * Class ModuleController
 * @package App\Setting\Controller\Modules
 * @Controller(prefix="setting/module")
 * @Auth
 */
class ModuleController extends MineController
{
    /**
     * @Inject
     * @var ModuleService
     */
    protected $service;

    /**
     * 本地模块列表
     * @GetMapping("index")
     * @Permission("setting:module:index")
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 新增本地模块
     * @PutMapping("save")
     * @Permission("setting:module:save")
     * @OperationLog
     * @param ModuleCreateRequest $request
     * @return ResponseInterface
     */
    public function save(ModuleCreateRequest $request): ResponseInterface
    {
        $this->service->createModule($request->validated());
        return $this->success();
    }

    /**
     * 删除模块
     * @DeleteMapping("delete/{name}")
     * @Permission("setting:module:delete")
     * @OperationLog
     * @param string $name
     * @return ResponseInterface
     */
    public function delete(string $name): ResponseInterface
    {
        return $this->service->deleteModule($name) ? $this->success() : $this->error();
    }

}