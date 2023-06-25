<?php

declare(strict_types = 1);
namespace App\System\Service;

use App\System\Mapper\SystemQueueLogMapper;
use App\System\Model\SystemUser;
use App\System\Queue\Producer\MessageProducer;
use App\System\Vo\AmqpQueueVo;
use App\System\Vo\QueueMessageVo;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Amqp\DelayProducer;
use Mine\Annotation\DependProxy;
use Mine\Exception\NormalStatusException;
use Mine\Interfaces\ServiceInterface\QueueLogServiceInterface;

/**
 * 队列管理服务类
 */
#[DependProxy(values: [ QueueLogServiceInterface::class ])]
class SystemQueueLogService extends AbstractService implements QueueLogServiceInterface
{
    /**
     * @var SystemQueueLogMapper
     */
    public $mapper;

    /**
     * @var SystemUserService
     */
    #[Inject]
    protected SystemUserService $userService;

    /**
     * @var DelayProducer
     */
    #[Inject]
    protected DelayProducer $producer;

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
     * @return bool
     */
    public function updateProduceStatus(string $ids): bool
    {
        // TODO...
        return true;
    }

    /**
     * 添加任务到队列
     * @param AmqpQueueVo $amqpQueueVo
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function addQueue(AmqpQueueVo $amqpQueueVo): bool
    {
        $producer = AnnotationCollector::get($amqpQueueVo->getProducer());

        $class = $amqpQueueVo->getProducer();

        if (! isset($producer['_c']['Hyperf\Amqp\Annotation\Producer'])) {
            throw new NormalStatusException(t('system.queue_annotation_not_open'), 500);
        }

        return $this->producer->produce(new $class($amqpQueueVo->getData()),
            $amqpQueueVo->getIsConfirm(),
            $amqpQueueVo->getTimeout(),
            $amqpQueueVo->getDelayTime()
        );
    }

    /**
     * 推送消息到队列
     * @param QueueMessageVo $message
     * @param array $receiveUsers
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function pushMessage(QueueMessageVo $message, array $receiveUsers = []): bool
    {
        $producer = AnnotationCollector::get(\App\System\Queue\Producer\MessageProducer::class);
        $consumer = AnnotationCollector::get(\App\System\Queue\Consumer\MessageConsumer::class);

        if (! isset($producer['_c']['Hyperf\Amqp\Annotation\Producer']) || ! isset($consumer['_c']['Hyperf\Amqp\Annotation\Consumer'])) {
            throw new NormalStatusException(t('system.queue_annotation_not_open'), 500);
        }

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
        );
    }
}