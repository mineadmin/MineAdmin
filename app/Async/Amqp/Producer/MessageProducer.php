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

namespace App\Async\Amqp\Producer;

use Hyperf\Amqp\Message\ProducerMessage;

/**
 * 后台内部消息队列生产处理.
 */
// #[Producer(exchange: "mineadmin", routingKey: "message.routing")]
class MessageProducer extends ProducerMessage
{
    public function __construct(mixed $data)
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
