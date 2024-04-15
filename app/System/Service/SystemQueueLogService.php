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

namespace App\System\Service;

use App\System\Mapper\SystemQueueLogMapper;
use App\System\Model\SystemUser;
use App\System\Queue\Consumer\MessageConsumer;
use App\System\Queue\Producer\MessageProducer;
use App\System\Vo\AmqpQueueVo;
use App\System\Vo\QueueMessageVo;
use Hyperf\Amqp\Producer;
use Hyperf\Codec\Json;
use Hyperf\Di\Annotation\AnnotationCollector;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Exception\NormalStatusException;
use Mine\Interfaces\ServiceInterface\QueueLogServiceInterface;
use Mine\Interfaces\ServiceInterface\QueueMessageServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 队列管理服务类.
 * @deprecated 2.0
 * @see https://github.com/mineadmin/MineAdmin/discussions/162
 */
#[DependProxy(values: [QueueLogServiceInterface::class])]
class SystemQueueLogService extends AbstractService implements QueueLogServiceInterface
{
    /**
     * @Message("生产成功")
     */
    public const PRODUCE_STATUS_SUCCESS = 3;

    /**
     * @Message("生产失败")
     */
    public const PRODUCE_STATUS_FAIL = 4;

    /**
     * @var SystemQueueLogMapper
     */
    public $mapper;

    /**
     * SystemQueueLogService constructor.
     */
    public function __construct(
        SystemQueueLogMapper $mapper,
        protected readonly Producer $producer,
        protected readonly SystemUserService $userService,
        protected readonly QueueMessageServiceInterface $queueMessageService
    ) {
        $this->mapper = $mapper;
    }

    /**
     * 修改队列日志的生产状态
     */
    public function updateProduceStatus(string $ids): bool
    {
        // TODO...
        return true;
    }

    /**
     * 添加任务到队列.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function addQueue(AmqpQueueVo $amqpQueueVo): bool
    {
        $producer = AnnotationCollector::get($amqpQueueVo->getProducer());

        $class = $amqpQueueVo->getProducer();

        if (! isset($producer['_c']['Hyperf\Amqp\Annotation\Producer'])) {
            throw new NormalStatusException(t('system.queue_annotation_not_open'), 500);
        }

        return $this->producer->produce($producer);
    }

    /**
     * 推送消息到队列.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function pushMessage(QueueMessageVo $message, array $receiveUsers = []): bool
    {
        $producer = AnnotationCollector::get(MessageProducer::class);
        $consumer = AnnotationCollector::get(MessageConsumer::class);

        if (! isset($producer['_c']['Hyperf\Amqp\Annotation\Producer']) || ! isset($consumer['_c']['Hyperf\Amqp\Annotation\Consumer'])) {
            throw new NormalStatusException(t('system.queue_annotation_not_open'), 500);
        }

        if (empty($message->getTitle())) {
            throw new NormalStatusException(t('system.queue_missing_message_title'), 500);
        }

        if (empty($message->getContent())) {
            throw new NormalStatusException(t('system.queue_missing_message_content_type'), 500);
        }

        if (empty($message->getContentType())) {
            throw new NormalStatusException(t('system.queue_missing_content'), 500);
        }

        if (empty($receiveUsers)) {
            $receiveUsers = $this->userService->pluck(['status' => SystemUser::USER_NORMAL], 'id');
        }
        $data = [
            'title' => $message->getTitle(),
            'content' => $message->getContent(),
            'content_type' => $message->getContentType(),
            'send_by' => $message->getSendBy() ?: user()->getId(),
            'receive_users' => $receiveUsers,
        ];
        $producer = new MessageProducer($data);
        $queueName = strchr($producer->getRoutingKey(), '.', true) . '.queue';
        $id = $this->save([
            'exchange_name' => $producer->getExchange(),
            'routing_key_name' => $producer->getRoutingKey(),
            'queue_name' => $queueName,
            'queue_content' => $producer->payload(),
            'delay_time' => 0,
            'produce_status' => self::PRODUCE_STATUS_SUCCESS,
        ]);
        $payload = Json::decode($producer->payload());

        $producer->setPayload([
            'queue_id' => $id, 'data' => $payload,
        ]);
        $this->queueMessageService->save(
            $payload
        );
        try {
            return $this->producer->produce(
                $producer
            );
        } catch (\Exception $e) {
            $this->update((int) $id, [
                'produce_status' => self::PRODUCE_STATUS_FAIL,
                'log_content' => $e->getMessage(),
            ]);
            throw new NormalStatusException($e->getMessage(), 500);
        }
    }
}
