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

namespace App\System\Controller\DataCenter;

use App\System\Request\MessageRequest;
use App\System\Service\SystemQueueMessageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Middlewares\CheckModuleMiddleware;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 信息管理控制器
 * Class MessageController.
 */
#[Controller(prefix: 'system/queueMessage'), Auth]
#[Middleware(middleware: CheckModuleMiddleware::class)]
class QueueMessageController extends MineController
{
    #[Inject]
    protected SystemQueueMessageService $service;

    /**
     * 接收消息列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('receiveList')]
    public function receiveList(): ResponseInterface
    {
        return $this->success($this->service->getReceiveMessage($this->request->all()));
    }

    /**
     * 已发送消息列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('sendList')]
    public function sendList(): ResponseInterface
    {
        return $this->success($this->service->getSendMessage($this->request->all()));
    }

    /**
     * 发私信
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Throwable
     */
    #[PostMapping('sendPrivateMessage')]
    public function sendPrivateMessage(MessageRequest $request): ResponseInterface
    {
        return $this->service->sendPrivateMessage($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 获取接收人列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getReceiveUser')]
    public function getReceiveUser(): ResponseInterface
    {
        return $this->success(
            $this->service->getReceiveUserList(
                (int) $this->request->input('id', 0),
                $this->request->all()
            )
        );
    }

    /**
     * 单个或批量删除数据到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('deletes')]
    public function deletes(): ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 更新状态到已读.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('updateReadStatus')]
    public function updateReadStatus(): ResponseInterface
    {
        return $this->service->updateDataStatus((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }
}
