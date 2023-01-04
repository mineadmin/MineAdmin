<?php
/**
 * Description:队列消费监听器
 * Created by phpStorm.
 * User: mike
 * Date: 2021/9/30
 * Time: 3:13 下午
 */
declare(strict_types=1);
namespace Mine\Amqp\Listener;

use App\System\Mapper\SystemQueueMessageMapper;
use App\System\Model\SystemQueueLog;
use App\System\Queue\Producer\MessageProducer;
use App\System\Service\SystemQueueLogService;
use Hyperf\Context\Context;
use Mine\Amqp\Event\AfterProduce;
use Mine\Amqp\Event\BeforeProduce;
use Mine\Amqp\Event\FailToProduce;
use Mine\Amqp\Event\ProduceEvent;
use Mine\Amqp\Event\WaitTimeout;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Event\Annotation\Listener;

/**
 * 生产队列监听
 * Class QueueProduceListener
 * @package Mine\Amqp\Listener
 */
#[Listener]
class QueueProduceListener implements ListenerInterface
{
    private SystemQueueLogService $service;

    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            AfterProduce::class,
            BeforeProduce::class,
            ProduceEvent::class,
            FailToProduce::class,
            WaitTimeout::class,
        ];
    }

    /**
     * @param object $event
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function process(object $event): void
    {
        $this->service = container()->get(SystemQueueLogService::class);
        $class = get_class($event);
        $func = lcfirst(trim(strrchr($class, '\\'),'\\'));
        $this->$func($event);
    }

    /**
     * Description:生产前
     * User:mike, x.mo
     * @param object $event
     */
    public function beforeProduce(object $event)
    {
        $queueName = strchr($event->producer->getRoutingKey(), '.', true) . '.queue';

        $id = $this->service->save([
            'exchange_name' => $event->producer->getExchange(),
            'routing_key_name' => $event->producer->getRoutingKey(),
            'queue_name' => $queueName,
            'queue_content' => $event->producer->payload(),
            'delay_time' => $event->delayTime ?? 0,
            'produce_status' => SystemQueueLog::PRODUCE_STATUS_SUCCESS
        ]);

        $this->setId($id);

        $payload = json_decode($event->producer->payload(), true);

        if (!isset($payload['queue_id'])) {
            $event->producer->setPayload([
                'queue_id' => $id, 'data' => $payload
            ]);
        }

        $this->service->update($id, [ 'queue_content' => $event->producer->payload() ]);
    }

    /**
     * Description:生产中
     * User:mike, x.mo
     * @param object $event
     */
    public function produceEvent(object $event): void
    {
        // TODO...
    }

    /**
     * Description:生产后
     * User:mike, x.mo
     * @param object $event
     */
    public function afterProduce(object $event): void
    {
        if (isset($event->producer) && $event->producer instanceof MessageProducer) {
            (new SystemQueueMessageMapper)->save(
                json_decode($event->producer->payload(), true)['data']
            );
        }
    }

    /**
     * Description:生产失败
     * User:mike, x.mo
     */
    public function failToProduce(object $event): void
    {
        $this->service->update((int) $this->getId(), [
            'produce_status' => SystemQueueLog::PRODUCE_STATUS_FAIL,
            'log_content' => $event->throwable ?: $event->throwable->getMessage()
        ]);
    }

    public function setId(int $id): void
    {
        Context::set('id', $id);
    }

    public function getId(): int
    {
        return Context::get('id', 0);
    }
}
