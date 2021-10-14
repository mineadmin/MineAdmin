<?php


namespace App\System\Controller\DataCenter;


use App\System\Service\DataMaintainService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DataMaintainController
 * @package App\System\Controller\DataCenter
 * @Controller(prefix="system/dataMaintain")
 * @Auth
 */
class DataMaintainController extends MineController
{
    /**
     * @Inject
     * @var DataMaintainService
     */
    public $service;

    /**
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:dataMaintain:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * @GetMapping("detailed")
     * @return ResponseInterface
     * @Permission("system:dataMaintain:detailed")
     */
    public function detailed(): ResponseInterface
    {
        return $this->success($this->service->getColumnList($this->request->input('table', null)));
    }

    /**
     * 优化表
     * @PostMapping("optimize")
     * @Permission("system:dataMaintain:optimize")
     * @OperationLog
     */
    public function optimize(): ResponseInterface
    {
        $tables = $this->request->input('tables', []);
        return $this->success($this->service->optimize($tables));
    }

    /**
     * 清理表碎片
     * @PostMapping("fragment")
     * @Permission("system:dataMaintain:fragment")
     * @OperationLog
     */
    public function fragment(): ResponseInterface
    {
        $tables = $this->request->input('tables', []);
        return $this->success($this->service->fragment($tables));
    }
}