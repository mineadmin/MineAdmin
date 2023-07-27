<?php
/**
 * 站内消息队列消费监听器
 */
declare(strict_types=1);
namespace App\System\Listener;

use Mine\Interfaces\ServiceInterface\QueueLogServiceInterface;
use Mine\Amqp\Event\AfterConsume;
use Mine\Amqp\Event\BeforeConsume;
use Mine\Amqp\Event\ConsumeEvent;
use Mine\Amqp\Event\FailToConsume;
use Mine\Amqp\Event\WaitTimeout;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Event\Annotation\Listener;

/**
 * 消费队列监听
 * Class QueueConsumeListener
 * @package Mine\Amqp\Listener
 */
#[Listener]
class QueueConsumeListener implements ListenerInterface
{
    /**
     * @Message("未消费")
     */
    const CONSUME_STATUS_NO = 1;
    /**
     * @Message("消费中")
     */
    const CONSUME_STATUS_DOING = 2;
    /**
     * @Message("消费成功")
     */
    const CONSUME_STATUS_SUCCESS = 3;
    /**
     * @Message("消费失败")
     */
    const CONSUME_STATUS_FAIL = 4;
    /**
     * @Message("消费重复")
     */
    const CONSUME_STATUS_REPEAT = 5;

    private QueueLogServiceInterface $service;

    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            AfterConsume::class,
            BeforeConsume::class,
            ConsumeEvent::class,
            FailToConsume::class,
            WaitTimeout::class,
        ];
    }

    /**
     * @param object $event
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        $this->service = container()->get(QueueLogServiceInterface::class);
        if ($event->message) {
            $class = get_class($event);
            $func = lcfirst(trim(strrchr($class, '\\'),'\\'));
            $this->$func($event);
        }
    }

    /**
     * Description:消费前
     * User:mike
     * @param object $event
     */
    public function beforeConsume(object $event)
    {
        $this->service->update(
            (int)$event->data['queue_id'],
            ['consume_status' => self::CONSUME_STATUS_DOING]
        );
    }

    /**
     * Description:消费中
     * User:mike
     * @param object $event
     */
    public function consumeEvent(object $event)
    {
        // TODO...
    }

    /**
     * Description:消费后
     * User:mike
     * @param object $event
     */
    public function afterConsume(object $event)
    {
        $this->service->update(
            (int)$event->data['queue_id'],
            ['consume_status' => self::CONSUME_STATUS_SUCCESS]
        );
    }

    /**
     * Description:消费失败
     * User:mike
     * @param object $event
     */
    public function failToConsume(object $event)
    {
        $this->service->update(
            (int)$event->data['queue_id'], [
            'consume_status' => self::CONSUME_STATUS_REPEAT,
            'log_content' => $event->throwable ?: $event->throwable->getMessage()
        ]);
    }
}
