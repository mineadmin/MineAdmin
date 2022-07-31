<?php

declare(strict_types=1);

namespace App\System\Queue\Producer;

use App\System\Service\SystemQueueLogService;
use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

/**
 * 后台内部消息队列生产处理
 */
//#[Producer(exchange: "mineadmin", routingKey: "message.routing")]
class MessageProducer extends ProducerMessage
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct($data)
    {
        console()->info(
            sprintf(
                'MineAdmin created queue message time at: %s, data is: %s',
                date('Y-m-d H:i:s'),
                (is_array($data) || is_object($data)) ? json_encode($data) : $data
            )
        );

        $this->payload = $data;
    }
}
