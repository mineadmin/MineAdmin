<?php
/**
 * 站内消息队列生产监听器.
 */
declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\System\Listener;

use App\System\Queue\Producer\MessageProducer;
use Hyperf\Context\Context;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Amqp\Event\AfterProduce;
use Mine\Amqp\Event\BeforeProduce;
use Mine\Amqp\Event\FailToProduce;
use Mine\Amqp\Event\ProduceEvent;
use Mine\Amqp\Event\WaitTimeout;
use Mine\Interfaces\ServiceInterface\QueueLogServiceInterface;
use Mine\Interfaces\ServiceInterface\QueueMessageServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 生产队列监听
 * Class QueueProduceListener.
 */
#[Listener]
class QueueProduceListener implements ListenerInterface
{
    /**
     * @Message("未生产")
     */
    public const PRODUCE_STATUS_WAITING = 1;

    /**
     * @Message("生产中")
     */
    public const PRODUCE_STATUS_DOING = 2;

    /**
     * @Message("生产成功")
     */
    public const PRODUCE_STATUS_SUCCESS = 3;

    /**
     * @Message("生产失败")
     */
    public const PRODUCE_STATUS_FAIL = 4;

    /**
     * @Message("生产重复")
     */
    public const PRODUCE_STATUS_REPEAT = 5;

    private QueueLogServiceInterface $service;

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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function process(object $event): void
    {
        $this->service = container()->get(QueueLogServiceInterface::class);
        $class = get_class($event);
        $func = lcfirst(trim(strrchr($class, '\\'), '\\'));
        $this->{$func}($event);
    }

    /**
     * Description:生产前
     * User:mike, x.mo.
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
            'produce_status' => self::PRODUCE_STATUS_SUCCESS,
        ]);

        $this->setId($id);

        $payload = json_decode($event->producer->payload(), true);

        if (! isset($payload['queue_id'])) {
            $event->producer->setPayload([
                'queue_id' => $id, 'data' => $payload,
            ]);
        }

        $this->service->update($id, ['queue_content' => $event->producer->payload()]);
    }

    /**
     * Description:生产中
     * User:mike, x.mo.
     */
    public function produceEvent(object $event): void
    {
        // TODO...
    }

    /**
     * Description:生产后
     * User:mike, x.mo.
     */
    public function afterProduce(object $event): void
    {
        // 只针对站内消息
        if (isset($event->producer) && $event->producer instanceof MessageProducer) {
            container()->get(QueueMessageServiceInterface::class)->save(
                json_decode($event->producer->payload(), true)['data']
            );
        }
    }

    /**
     * Description:生产失败
     * User:mike, x.mo.
     */
    public function failToProduce(object $event): void
    {
        $this->service->update((int) $this->getId(), [
            'produce_status' => self::PRODUCE_STATUS_FAIL,
            'log_content' => $event->throwable ?: $event->throwable->getMessage(),
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
