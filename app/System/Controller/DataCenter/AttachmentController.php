<?php

declare(strict_types=1);
namespace App\System\Controller\DataCenter;

use App\System\Service\SystemUploadFileService;
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
 * 文件管理控制器
 * Class UploadFileController
 * @package App\System\Controller\DataCenter
 * @Controller(prefix="system/attachment")
 * @Auth
 */
class AttachmentController extends MineController
{
    /**
     * 上传文件服务
     * @Inject
     * @var SystemUploadFileService
     */
    protected $service;

    /**
     * 列表数据
     * @GetMapping("index")
     * @return ResponseInterface
     * @Permission("system:attachment:index")
     */
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 回收站列表数据
     * @GetMapping("recycle")
     * @return ResponseInterface
     * @Permission("system:attachment:recycle")
     */
    public function recycle(): ResponseInterface
    {
        return $this->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 单个或批量删除附件
     * @DeleteMapping("delete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:attachment:delete")
     */
    public function delete(String $ids): ResponseInterface
    {
        return $this->service->delete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量真实删除文件 （清空回收站）
     * @DeleteMapping("realDelete/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:attachment:realDelete")
     * @OperationLog
     */
    public function realDelete(String $ids): ResponseInterface
    {
        return $this->service->realDelete($ids) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量恢复在回收站的文件
     * @PutMapping("recovery/{ids}")
     * @param String $ids
     * @return ResponseInterface
     * @Permission("system:attachment:recovery")
     */
    public function recovery(String $ids): ResponseInterface
    {
        return $this->service->recovery($ids) ? $this->success() : $this->error();
    }
}
