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

use Hyperf\Amqp\Message\ConsumerMessageInterface;
use Hyperf\Amqp\Message\ProducerMessageInterface;
use Throwable;

class FailToProduce extends ConsumeEvent
{
    /**
     * @var Throwable
     */
    public $throwable;

    public function __construct(ProducerMessageInterface $producer, Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}
