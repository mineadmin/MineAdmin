<?php

declare(strict_types=1);

namespace App\System\Queue\Consumer;

use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 后台内部消息队列消费处理
 */
#[Consumer(exchange: "mineadmin", routingKey: "message.routing", queue: "message.queue", name: "message.queue", nums: 1)]
class MessageConsumer extends ConsumerMessage
{
    /**
     * @param $data
     * @param AMQPMessage $message
     * @return string
     */
    public function consumeMessage($data, AMQPMessage $message): string
    {
        print_r($data);
        console()->error('cm');
        return parent::consumeMessage($data, $message);
    }

    public function consume($data): string
    {
        return empty($data) ? Result::DROP : Result::ACK;
    }

    /**
     * 设置是否启动amqp
     * @return bool
     */
    public function isEnable(): bool
    {
        return env('AMQP_ENABLE', false);
    }
}
