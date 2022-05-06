<?php

declare(strict_types = 1);
namespace App\System\Service;

use App\System\Mapper\SystemNoticeMapper;
use App\System\Model\SystemQueueMessage;
use App\System\Vo\QueueMessageVo;
use Co\System;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

/**
 * 通知管理服务类
 */
class SystemNoticeService extends AbstractService
{
    /**
     * @var SystemNoticeMapper
     */
    public $mapper;

    public function __construct(SystemNoticeMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 保存公告
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Throwable
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[Transaction]
    public function save(array $data): int
    {
        $message = new QueueMessageVo();
        $message->setTitle($data['title']);
        $message->setContentType(
            $data['type'] === '1'
                ? SystemQueueMessage::TYPE_NOTICE
                : SystemQueueMessage::TYPE_ANNOUNCE
        );
        $message->setContent($data['content']);
        $message->setSendBy(user()->getId());
        $data['message_id'] = push_queue_message($message, $data['users']);

        if ($data['message_id'] !== -1) {
            return parent::save($data);
        }

        throw new NormalStatusException;
    }

}
