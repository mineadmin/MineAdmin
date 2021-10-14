<?php

declare(strict_types = 1);
namespace App\Setting\Controller\Tools;

use App\Setting\Request\Tool\GenerateUpdateRequest;
use App\Setting\Request\Tool\LoadTableRequest;
use App\Setting\Service\SettingGenerateColumnsService;
use App\Setting\Service\SettingGenerateTablesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 代码生成器控制器
 * Class CodeController
 * @package App\Setting\Controller\Tools
 * @Controller(prefix="setting/code")
 * @Auth
 */
class GenerateCodeController extends MineController
{
    /**
     * 信息表服务
     * @Inject
     * @var SettingGenerateTablesService
     */
    protected $tableService;

    /**
     * 信息字段表服务
     * @Inject
     * @var SettingGenerateColumnsService
     */
    protected $columnService;

    /**
     * 代码生成列表分页
     * @GetMapping("index")
     * @Permission("setting:code:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->tableService->getPageList($this->request->All()));
    }

    /**
     * 获取业务表字段信息
     * @GetMapping("getTableColumns")
     */
    public function getTableColumns(): ResponseInterface
    {
        return $this->success($this->columnService->getList($this->request->all()));
    }

    /**
     * 预览代码
     * @GetMapping("preview")
     * @Permission("setting:code:preview")
     * @throws \Exception
     */
    public function preview(): ResponseInterface
    {
        return $this->success($this->tableService->preview((int) $this->request->input('id', 0)));
    }

    /**
     * 更新业务表信息
     * @PostMapping("update")
     * @Permission("setting:code:update")
     * @param GenerateUpdateRequest $request
     * @return ResponseInterface
     */
    public function update(GenerateUpdateRequest $request): ResponseInterface
    {
        return $this->success($this->tableService->updateTableAndColumns($request->validated()));
    }

    /**
     * 生成代码
     * @PostMapping("generate/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("setting:code:generate")
     * @throws \Exception
     */
    public function generate(string $ids): ResponseInterface
    {
        return $this->_download($this->tableService->generate($ids), 'mineadmin.zip');
    }

    /**
     * 加载数据表
     * @PostMapping("loadTable")
     * @Permission("setting:code:loadTable")
     * @OperationLog
     * @param LoadTableRequest $request
     * @return ResponseInterface
     */
    public function loadTable(LoadTableRequest $request): ResponseInterface
    {
        return $this->tableService->loadTable($request->input('names')) ? $this->success() : $this->error();
    }

    /**
     * 删除代码生成表
     * @DeleteMapping("delete/{ids}")
     * @Permission("setting:code:delete")
     * @OperationLog
     * @param string $ids
     * @return ResponseInterface
     */
    public function delete(string $ids): ResponseInterface
    {
        return $this->success($this->tableService->delete($ids));
    }

    /**
     * 同步数据库中的表信息跟字段
     * @PutMapping("sync/{id}")
     * @Permission("setting:code:sync")
     * @OperationLog
     * @param int $id
     * @return ResponseInterface
     */
    public function sync(int $id): ResponseInterface
    {
        return $this->success($this->tableService->sync($id));
    }
}