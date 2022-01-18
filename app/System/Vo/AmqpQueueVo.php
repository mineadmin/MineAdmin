<?php
declare(strict_types = 1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\System\Vo;

/**
 * 队列内容对象
 * Class AmqpQueueVo
 * @package App\System\Vo
 * @author X.Mo
 */
class AmqpQueueVo
{
    /**
     * 生产对象
     * @var string
     */
    protected string $producer;

    /**
     * 队列数据
     * @var array
     */
    protected array $data;

    /**
     * 是否需要确认
     * @var bool
     */
    protected bool $isConfirm = false;

    /**
     * 队列超时时间
     * @var integer
     */
    protected int $timeout = 5;

    /**
     * 队列延迟生产时间秒
     * @var integer
     */
    protected int $delayTime = 0;

    /**
     * @return string
     */
    public function getProducer(): string
    {
        return $this->producer;
    }

    /**
     * @param string $producer
     * @return AmqpQueueVo
     */
    public function setProducer(string $producer): AmqpQueueVo
    {
        $this->producer = $producer;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return AmqpQueueVo
     */
    public function setData(array $data): AmqpQueueVo
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsConfirm(): bool
    {
        return $this->isConfirm;
    }

    /**
     * @param bool $isConfirm
     * @return AmqpQueueVo
     */
    public function setIsConfirm(bool $isConfirm): AmqpQueueVo
    {
        $this->isConfirm = $isConfirm;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return AmqpQueueVo
     */
    public function setTimeout(int $timeout): AmqpQueueVo
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getDelayTime(): int
    {
        return $this->delayTime;
    }

    /**
     * @param int $delayTime
     * @return AmqpQueueVo
     */
    public function setDelayTime(int $delayTime): AmqpQueueVo
    {
        $this->delayTime = $delayTime;
        return $this;
    }


}