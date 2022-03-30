<?php

declare(strict_types=1);

namespace App\System\Queue\Consumer;

use App\System\Model\SystemQueueMessage;
use App\System\Service\SystemQueueMessageService;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 后台内部消息队列消费处理
 */
#[Consumer(exchange: "mineadmin", routingKey: "message.routing", queue: "message.queue", name: "message.queue", nums: 1)]
class MessageConsumer extends ConsumerMessage
{
    /**
     * @Inject
     * @var SystemQueueMessageService
     */
    protected $service;

    /**
     * @param $data
     * @param AMQPMessage $message
     * @return string
     */
    public function consumeMessage($data, AMQPMessage $message): string
    {
        parent::consumeMessage($data, $message);

        if(empty($data['data'])) {
            return Result::DROP;
        }

        return Result::ACK;
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
