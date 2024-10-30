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

namespace App\Http\Admin\Controller;

use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\UploadRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\AttachmentSchema;
use App\Service\AttachmentService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;
use Symfony\Component\Finder\SplFileInfo;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class AttachmentController extends AbstractController
{
    public function __construct(
        protected readonly AttachmentService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/attachment/list',
        operationId: 'AttachmentList',
        summary: '附件列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['数据中心'],
    )]
    #[Permission(code: 'dataCenter:attachment:list')]
    #[PageResponse(instance: AttachmentSchema::class)]
    public function list(): Result
    {
        $params = $this->getRequest()->all();
        $params['current_user_id'] = $this->currentUser->id();
        if (isset($params['suffix'])) {
            $params['suffix'] = explode(',', $params['suffix']);
        }
        return $this->success(
            $this->service->page($params, $this->getCurrentPage(), $this->getPageSize())
        );
    }

    #[Post(
        path: '/admin/attachment/upload',
        operationId: 'UploadAttachment',
        summary: '上传附件',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['数据中心'],
    )]
    #[Permission(code: 'dataCenter:attachment:upload')]
    #[ResultResponse(instance: new Result())]
    public function upload(UploadRequest $request): Result
    {
        $uploadFile = $request->file('file');
        $newTmpPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $uploadFile->getExtension();
        $uploadFile->moveTo($newTmpPath);
        $splFileInfo = new SplFileInfo($newTmpPath, '', '');
        return $this->success(
            $this->service->upload($splFileInfo, $uploadFile, $this->currentUser->id())
        );
    }

    #[Delete(
        path: '/admin/attachment/{id}',
        operationId: 'DeleteAttachment',
    )]
    #[Permission(code: 'dataCenter:attachment:delete')]
    #[ResultResponse(instance: new Result())]
    public function delete(int $id): Result
    {
        if (! $this->service->getRepository()->existsById($id)) {
            return $this->error(trans('attachment.attachment_not_exist'));
        }
        $this->service->deleteById($id);
        return $this->success();
    }
}
