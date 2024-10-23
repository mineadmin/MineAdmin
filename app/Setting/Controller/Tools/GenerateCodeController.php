<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Setting\Controller\Tools;

use App\Setting\Request\GenerateRequest;
use App\Setting\Service\SettingDatasourceService;
use App\Setting\Service\SettingGenerateColumnsService;
use App\Setting\Service\SettingGenerateTablesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Middlewares\CheckModuleMiddleware;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 代码生成器控制器
 * Class CodeController.
 */
#[Controller(prefix: 'setting/code'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
class GenerateCodeController extends MineController
{
    /**
     * 信息表服务
     */
    #[Inject]
    protected SettingGenerateTablesService $tableService;

    /**
     * 数据源处理服务
     * SettingDatasourceService.
     */
    #[Inject]
    protected SettingDatasourceService $datasourceService;

    /**
     * 信息字段表服务
     */
    #[Inject]
    protected SettingGenerateColumnsService $columnService;

    /**
     * 代码生成列表分页.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('setting:code')]
    public function index(): ResponseInterface
    {
        return $this->success($this->tableService->getPageList($this->request->All()));
    }

    /**
     * 获取数据源列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getDataSourceList'), Permission('setting:code')]
    public function getDataSourceList(): ResponseInterface
    {
        return $this->success($this->datasourceService->getPageList([
            'select' => 'id as value, source_name as label',
        ]));
    }

    /**
     * 获取业务表字段信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getTableColumns')]
    public function getTableColumns(): ResponseInterface
    {
        return $this->success($this->columnService->getList($this->request->all()));
    }

    /**
     * 预览代码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    #[GetMapping('preview'), Permission('setting:code:preview')]
    public function preview(): ResponseInterface
    {
        return $this->success($this->tableService->preview((int) $this->request->input('id', 0)));
    }

    /**
     * 读取表数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('readTable')]
    public function readTable(): ResponseInterface
    {
        return $this->success($this->tableService->read((int) $this->request->input('id')));
    }

    /**
     * 更新业务表信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('update'), Permission('setting:code:update')]
    public function update(GenerateRequest $request): ResponseInterface
    {
        return $this->tableService->updateTableAndColumns($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 生成代码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('generate'), Permission('setting:code:generate'), OperationLog]
    public function generate(): ResponseInterface
    {
        return $this->_download(
            $this->tableService->generate((array) $this->request->input('ids', [])),
            'mineadmin.zip'
        );
    }

    /**
     * 加载数据表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('loadTable'), Permission('setting:code:loadTable'), OperationLog]
    public function loadTable(GenerateRequest $request): ResponseInterface
    {
        return $this->tableService->loadTable($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 删除代码生成表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('setting:code:delete'), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->tableService->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 同步数据库中的表信息跟字段.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('sync/{id}'), Permission('setting:code:sync'), OperationLog]
    public function sync(int $id): ResponseInterface
    {
        return $this->tableService->sync($id) ? $this->success() : $this->error();
    }

    /**
     * 获取所有启用状态模块下的所有模型.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getModels')]
    public function getModels(): ResponseInterface
    {
        return $this->success($this->tableService->getModels());
    }
}
