<?php

declare(strict_types=1);
namespace App\Setting\Controller\Tools;

use App\Setting\Request\Tool\TableCreateRequest;
use App\Setting\Service\TableService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * 表设计器
 * Class TableController
 * @package App\Setting\Controller\Tools
 */
#[Controller(prefix: "setting/table"), Auth]
class TableController extends MineController
{
    #[Inject]
    protected TableService $service;

    /**
     * 获取系统信息
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getSystemInfo")]
    public function getSystemInfo(): \Psr\Http\Message\ResponseInterface
    {
        $this->mine->scanModule();
        return $this->success([
            'tablePrefix' => $this->service->getTablePrefix(),
            'modulesList' => $this->mine->getModuleInfo()
        ]);
    }

    /**
     * 创建数据表
     * @param TableCreateRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping("save"), Permission("setting:table:save"), OperationLog]
    public function save(TableCreateRequest $request): \Psr\Http\Message\ResponseInterface
    {
        if ($this->service->createTable($request->validated())) {
            return $this->success();
        } else {
            return $this->error();
        }
    }
}