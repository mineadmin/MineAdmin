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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function Hyperf\Translation\trans;

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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping(path: '{table_id}')]
    public function table(mixed $table_id)
    {
        $table = $this->tablesService->read($table_id)?->toArray();
        if (empty($table)) {
            return $this->error(trans('setting.auto_form.table_not_found'));
        }
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
        $table = $this->tablesService->read($table_id)?->toArray();
        if (empty($table)) {
            return $this->error(trans('setting.auto_form.table_not_found'));
        }
        if ($table['type'] === 'tree') {
            $data = $this->service->getTreeList($table_id, $this->request->all());
        } else {
            $data = $this->service->getPageList($table_id, $this->request->all());
        }
        return $this->success($data);
    }

    #[GetMapping('tree/{table_id}')]
    public function tree($table_id): ResponseInterface
    {
        return $this->success($this->service->getSelectTree($table_id));
    }

    /**
     * 新增.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save/{table_id}')]
    public function save($table_id): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($table_id, $this->request->all())]);
    }

    /**
     * 更新.
     * @param int $id
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{table_id}/{id}')]
    public function update(mixed $table_id, mixed $id): ResponseInterface
    {
        return $this->service->update($table_id, $id, $this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus/{table_id}')]
    public function changeStatus($table_id): ResponseInterface
    {
        return $this->service->changeStatus(
            $table_id,
            (int) $this->request->input('id'),
            (string) $this->request->input('statusValue'),
            (string) $this->request->input('statusName', 'status')
        ) ? $this->success() : $this->error();
    }

    /**
     * 回收站角色分页列表.
     */
    #[GetMapping('recycle/{table_id}')]
    public function recycle($table_id): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($table_id, $this->request->all()));
    }

    /**
     * 单个或批量真实删除数据 （清空回收站）.
     */
    #[DeleteMapping('realDelete/{table_id}')]
    public function realDelete($table_id): ResponseInterface
    {
        return $this->service->realDelete($table_id, (array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的数据.
     */
    #[PutMapping('recovery/{table_id}')]
    public function recovery($table_id): ResponseInterface
    {
        return $this->service->recovery($table_id, (array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 数据导入.
     */
    #[PostMapping('import')]
    public function import(): ResponseInterface
    {
        return $this->error('未实现');
    }

    /**
     * 数据导出.
     */
    #[PostMapping('export')]
    public function export(): ResponseInterface
    {
        return $this->error('未实现');
    }
}
