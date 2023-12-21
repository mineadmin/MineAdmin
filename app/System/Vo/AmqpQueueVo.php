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

namespace App\System\Vo;

/**
 * 队列内容对象
 * Class AmqpQueueVo.
 */
class AmqpQueueVo
{
    /**
     * 生产对象
     */
    protected string $producer;

    /**
     * 队列数据.
     */
    protected array $data;

    /**
     * 是否需要确认.
     */
    protected bool $isConfirm = false;

    /**
     * 队列超时时间.
     */
    protected int $timeout = 5;

    /**
     * 队列延迟生产时间秒.
     */
    protected int $delayTime = 0;

    public function getProducer(): string
    {
        return $this->producer;
    }

    public function setProducer(string $producer): AmqpQueueVo
    {
        $this->producer = $producer;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): AmqpQueueVo
    {
        $this->data = $data;
        return $this;
    }

    public function getIsConfirm(): bool
    {
        return $this->isConfirm;
    }

    public function setIsConfirm(bool $isConfirm): AmqpQueueVo
    {
        $this->isConfirm = $isConfirm;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): AmqpQueueVo
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getDelayTime(): int
    {
        return $this->delayTime;
    }

    public function setDelayTime(int $delayTime): AmqpQueueVo
    {
        $this->delayTime = $delayTime;
        return $this;
    }
}
