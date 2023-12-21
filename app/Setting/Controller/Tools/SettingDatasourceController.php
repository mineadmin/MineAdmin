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

use App\Setting\Request\SettingDatasourceRequest;
use App\Setting\Service\SettingDatasourceService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Annotation\RemoteState;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 数据源管理控制器
 * Class SettingDatasourceController.
 */
#[Controller(prefix: 'setting/datasource'), Auth]
class SettingDatasourceController extends MineController
{
    /**
     * 业务处理服务
     * SettingDatasourceService.
     */
    #[Inject]
    protected SettingDatasourceService $service;

    /**
     * 列表.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('setting:datasource, setting:datasource:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 新增.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('setting:datasource:save'), OperationLog]
    public function save(SettingDatasourceRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('setting:datasource:update'), OperationLog]
    public function update(int $id, SettingDatasourceRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('read/{id}'), Permission('setting:datasource:read')]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('setting:datasource:delete'), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 数据导入.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('import'), Permission('setting:datasource:import')]
    public function import(): ResponseInterface
    {
        return $this->service->import(\App\Setting\Dto\SettingDatasourceDto::class) ? $this->success() : $this->error();
    }

    /**
     * 下载导入模板
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('downloadTemplate')]
    public function downloadTemplate(): ResponseInterface
    {
        return (new \Mine\MineCollection())->export(\App\Setting\Dto\SettingDatasourceDto::class, '模板下载', []);
    }

    /**
     * 数据导出.
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('export'), Permission('setting:datasource:export'), OperationLog]
    public function export(): ResponseInterface
    {
        return $this->service->export($this->request->all(), \App\Setting\Dto\SettingDatasourceDto::class, '导出数据列表');
    }

    /**
     * 测试数据库连接.
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('testLink')]
    public function testLink(): ResponseInterface
    {
        return $this->service->testLink($this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 获取数据源的表列表.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('getDataSourceTablePageList')]
    public function getDataSourceTablePageList(): ResponseInterface
    {
        return $this->success(
            $this->service->getDataSourceTablePageList($this->request->all())
        );
    }

    /**
     * 远程万能通用列表接口.
     */
    #[PostMapping('remote'), RemoteState(true)]
    public function remote(): ResponseInterface
    {
        return $this->success($this->service->getRemoteList($this->request->all()));
    }
}
