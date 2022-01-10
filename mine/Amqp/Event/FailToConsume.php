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
use Throwable;

class FailToConsume
{
    /**
     * @var Throwable
     */
    public $throwable;
    /**
     * @var ConsumerMessageInterface
     */
    public $message;
    public $data;

    public function __construct($message, $data, Throwable $throwable)
    {
        $this->throwable = $throwable;
        $this->message = $message;
        $this->data = $data;
        
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}
