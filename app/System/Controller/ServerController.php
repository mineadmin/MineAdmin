<?php
declare(strict_types=1);

/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\System\Controller;

use App\System\Service\SystemQueueMessageService;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class ServerController
 * @package App\System\Controller
 */
class ServerController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    /**
     * @var int
     */
    protected $uid;

    /**
     * 成功连接到 ws 回调
     * @param Response|Server $server
     * @param Request $request
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onOpen($server, $request): void
    {
        $this->uid = user()->getUserInfo(
            container()->get(ServerRequestInterface::class)->getQueryParams()['token']
        )['id'];

        console()->info(
            "WebSocket [ user connection to message server: id > {$this->uid}, ".
            "fd > {$request->fd}, time > ". date('Y-m-d H:i:s') .' ]'
        );
    }

    /**
     * 消息回调
     * @param Response|Server $server
     * @param Frame $frame
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onMessage($server, $frame): void
    {
        $data = json_decode($frame->data, true);
        switch($data['event']) {
            case 'get_unread_message':
                $service = container()->get(SystemQueueMessageService::class);
                $server->push($frame->fd, json_encode([
                    'event' => 'ev_new_message',
                    'message' => 'success',
                    'data' => $service->getUnreadMessage($this->uid)['items']
                ]));
                break;
        }
    }

    /**
     * 关闭 ws 连接回调
     * @param Response|\Swoole\Server $server
     * @param int $fd
     * @param int $reactorId
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        console()->info(
            "WebSocket [ user close connect for message server: id > {$this->uid}, ".
            "fd > {$fd}, time > ". date('Y-m-d H:i:s') .' ]'
        );
    }
}