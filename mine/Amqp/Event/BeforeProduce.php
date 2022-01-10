<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Mine\Amqp\Event;

use Hyperf\Amqp\Message\ProducerMessageInterface;

class BeforeProduce
{
    public $producer;
    public $delayTime;

    public function __construct(ProducerMessageInterface $producer, int $delayTime)
    {
        $this->producer = $producer;
        $this->delayTime = $delayTime;
    }
}
