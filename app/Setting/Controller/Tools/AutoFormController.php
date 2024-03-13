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

use App\Setting\Service\AutoFormService;
use App\Setting\Service\SettingGenerateColumnsService;
use App\Setting\Service\SettingGenerateTablesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'setting/autoform'), Auth]
class AutoFormController extends MineController
{
    #[Inject]
    public SettingGenerateTablesService $tablesService;

    #[Inject]
    public SettingGenerateColumnsService $columnsService;

    #[Inject]
    protected AutoFormService $service;

    /**
     * 配置信息.
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping(path: '{table_id}')]
    public function table(mixed $table_id)
    {
        $table = $this->tablesService->read($table_id)->toArray();
        $columns = $this->columnsService->getList(['table_id' => $table_id]);
        $table['columns'] = $columns;
        return $this->response->success('ok', $table);
    }

    /**
     * 列表.
     */
    #[GetMapping('index/{table_id}')]
    public function index($table_id): ResponseInterface
    {
        return $this->success($this->service->getPageList($table_id, $this->request->all()));
    }

    /**
     * 新增.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('save/{table_id}')]
    public function save($table_id): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($table_id, $this->request->all())]);
    }

    /**
     * 更新.
     * @param int $id
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping('update/{table_id}/{id}')]
    public function update(mixed $table_id, mixed $id): ResponseInterface
    {
        return $this->service->update($table_id, $id, $this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('read/{table_id}/{id}')]
    public function read(mixed $table_id, int $id): ResponseInterface
    {
        return $this->success($this->service->read($table_id, $id));
    }

    #[DeleteMapping('delete/{table_id}')]
    public function delete(mixed $table_id): ResponseInterface
    {
        $ids = (array) $this->request->input('ids', []);
        return $this->service->delete($table_id, $ids) ? $this->success() : $this->error();
    }

    /**
     * 更改数据状态
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus')]
    public function changeStatus(): ResponseInterface
    {
        return $this->service->changeStatus(
            (int) $this->request->input('id'),
            (string) $this->request->input('statusValue'),
            (string) $this->request->input('statusName', 'status')
        ) ? $this->success() : $this->error();
    }

    /**
     * 回收站角色分页列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle/{table_id}')]
    public function recycle($table_id): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($table_id, $this->request->all()));
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete/{table_id}')]
    public function realDelete($table_id): ResponseInterface
    {
        return $this->service->realDelete($table_id, (array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PutMapping('recovery/{table_id}')]
    public function recovery($table_id): ResponseInterface
    {
        return $this->service->recovery($table_id, (array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 数据导入.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('import')]
    public function import(): ResponseInterface
    {
        return $this->error('未实现');
        // return $this->service->import(\App\Test\Dto\TestCrontabDto::class) ? $this->success() : $this->error();
    }

    /**
     * 数据导出.
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('export')]
    public function export(): ResponseInterface
    {
        return $this->error('未实现');
        // return $this->service->export($this->request->all(), \App\Test\Dto\TestCrontabDto::class, '导出数据列表');
    }
}
