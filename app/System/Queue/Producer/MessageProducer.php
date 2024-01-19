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

namespace App\System\Queue\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 后台内部消息队列生产处理.
 */
// #[Producer(exchange: "mineadmin", routingKey: "message.routing")]
class MessageProducer extends ProducerMessage
{
    /**
     * @param mixed $data
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
