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

namespace App\System\Queue\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Mine\Interfaces\ServiceInterface\QueueLogServiceInterface;

/**
 * 后台内部消息队列消费处理.
 */
// #[Consumer(exchange: "mineadmin", routingKey: "message.routing", queue: "message.queue", name: "message.queue", nums: 1)]
class MessageConsumer extends ConsumerMessage
{
    /**
     * @Message("消费成功")
     */
    public const CONSUME_STATUS_SUCCESS = 3;

    /**
     * @Message("消费失败")
     */
    public const CONSUME_STATUS_FAIL = 4;

    public function __construct(
        private readonly QueueLogServiceInterface $service
    ) {}

    public function consume($data): Result
    {
        if (empty($data)) {
            return Result::DROP;
        }
        $queueId = (int) $data['queue_id'];
        try {
            $this->service->update(
                $queueId,
                ['consume_status' => self::CONSUME_STATUS_SUCCESS]
            );
        } catch (\Exception $e) {
            $this->service->update(
                $queueId,
                [
                    'consume_status' => self::CONSUME_STATUS_FAIL,
                    'log_content' => $e->getMessage(),
                ]
            );
        }
        return Result::ACK;
    }

    /**
     * 设置是否启动amqp.
     */
    public function isEnable(): bool
    {
        return env('AMQP_ENABLE', false);
    }
}
