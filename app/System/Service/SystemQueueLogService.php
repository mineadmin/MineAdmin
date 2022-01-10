<?php

declare(strict_types = 1);
namespace App\System\Service;

use App\System\Mapper\SystemQueueLogMapper;
use App\System\Model\SystemUser;
use App\System\Queue\Producer\MessageProducer;
use App\System\Vo\QueueMessageVo;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Amqp\DelayProducer;
use Mine\Exception\NormalStatusException;

/**
 * 队列管理服务类
 */
class SystemQueueLogService extends AbstractService
{
    /**
     * @var SystemQueueLogMapper
     */
    public $mapper;

    /**
     * @Inject
     * @var SystemUserService
     */
    public $userService;

    /**
     * @Inject
     * @var DelayProducer
     */
    protected $producer;

    /**
     * SystemQueueLogService constructor.
     * @param SystemQueueLogMapper $mapper
     */
    public function __construct(SystemQueueLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 修改队列日志的生产状态
     * @param string $ids
     * @return bool
     */
    public function updateProduceStatus(string $ids): bool
    {
        // TODO...
        return true;
    }

    /**
     * 推送消息到队列
     * @param QueueMessageVo $message
     * @param array $receiveUsers
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function pushMessage(QueueMessageVo $message, array $receiveUsers = []): int
    {
        if (empty ($message->getTitle())) {
            throw new NormalStatusException(t('system.queue_missing_message_title'), 500);
        }

        if (empty ($message->getContent())) {
            throw new NormalStatusException(t('system.queue_missing_message_content_type'), 500);
        }

        if (empty ($message->getContentType())) {
            throw new NormalStatusException(t('system.queue_missing_content'), 500);
        }

        if (empty($receiveUsers)) {
            $receiveUsers = $this->userService->pluck(['status' => SystemUser::USER_NORMAL],'id');
        }

        $data = [
            'id'            => (int) snowflake_id(),
            'title'         => $message->getTitle(),
            'content'       => $message->getContent(),
            'content_type'  => $message->getContentType(),
            'send_by'       => $message->getSendBy() ?: user()->getId(),
            'receive_users' => $receiveUsers,
        ];

        return $this->producer->produce(
            new MessageProducer($data),
            $message->getIsConfirm(),
            $message->getTimeout(),
            $message->getDelayTime()
        ) ? $data['id'] : -1;
    }
}