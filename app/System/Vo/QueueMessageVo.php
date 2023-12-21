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
 * 队列消息内容对象
 * Class QueueMessageVo.
 */
class QueueMessageVo
{
    /**
     * 消息标题.
     */
    protected string $title;

    /**
     * 消息类型.
     */
    protected string $contentType;

    /**
     * 消息内容.
     */
    protected string $content;

    /**
     * 发送人.
     */
    protected int $sendBy;

    /**
     * 备注.
     */
    protected string $remark;

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

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(string $title): QueueMessageVo
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return $this
     */
    public function setContentType(string $contentType): QueueMessageVo
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return $this
     */
    public function setContent(string $content): QueueMessageVo
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getSendBy(): int
    {
        return $this->sendBy;
    }

    /**
     * @param string $sendBy
     */
    public function setSendBy(int $sendBy): QueueMessageVo
    {
        $this->sendBy = $sendBy;
        return $this;
    }

    public function getRemark(): string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): QueueMessageVo
    {
        $this->remark = $remark;
        return $this;
    }

    public function getIsConfirm(): bool
    {
        return $this->isConfirm;
    }

    public function setIsConfirm(bool $isConfirm): QueueMessageVo
    {
        $this->isConfirm = $isConfirm;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): QueueMessageVo
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getDelayTime(): int
    {
        return $this->delayTime;
    }

    public function setDelayTime(int $delayTime): QueueMessageVo
    {
        $this->delayTime = $delayTime;
        return $this;
    }
}
