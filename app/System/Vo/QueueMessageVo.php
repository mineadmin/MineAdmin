<?php

namespace App\System\Vo;

/**
 * 队列消息内容对象
 * Class QueueMessageVo
 * @package App\System\Vo
 */
class QueueMessageVo
{
    /**
     * 消息标题
     * @var string
     */
    protected $title;

    /**
     * 消息类型
     * @var string
     */
    protected $contentType;

    /**
     * 消息内容
     * @var string
     */
    protected $content;

    /**
     * 发送人
     * @var string
     */
    protected $sendBy;

    /**
     * 备注
     * @var string
     */
    protected $remark;

    /**
     * 是否需要确认
     * @var bool
     */
    protected $isConfirm = false;

    /**
     * 队列超时时间
     * @var integer
     */
    protected $timeout = 5;

    /**
     * 队列延迟生产时间秒
     * @var integer
     */
    protected $delayTime = 0;

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
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
     * @param string $contentType
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
     * @param string $content
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
    public function getSendBy(): string
    {
        return $this->sendBy;
    }

    /**
     * @param string $sendBy
     * @return QueueMessageVo
     */
    public function setSendBy(string $sendBy): QueueMessageVo
    {
        $this->sendBy = $sendBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     * @return QueueMessageVo
     */
    public function setRemark(string $remark): QueueMessageVo
    {
        $this->remark = $remark;
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
     * @return QueueMessageVo
     */
    public function setIsConfirm(bool $isConfirm): QueueMessageVo
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
     * @return QueueMessageVo
     */
    public function setTimeout(int $timeout): QueueMessageVo
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
     * @return QueueMessageVo
     */
    public function setDelayTime(int $delayTime): QueueMessageVo
    {
        $this->delayTime = $delayTime;
        return $this;
    }



}