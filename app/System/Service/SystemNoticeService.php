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

namespace App\System\Service;

use App\System\Mapper\SystemNoticeMapper;
use App\System\Mapper\SystemUserMapper;
use App\System\Model\SystemQueueMessage;
use App\System\Vo\QueueMessageVo;
use Hyperf\Snowflake\IdGenerator\SnowflakeIdGenerator;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\MineModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 通知管理服务类.
 */
class SystemNoticeService extends AbstractService
{
    /**
     * @var SystemNoticeMapper
     */
    public $mapper;

    public function __construct(
        SystemNoticeMapper $mapper,
        private readonly SnowflakeIdGenerator $idGenerator
    ) {
        $this->mapper = $mapper;
    }

    /**
     * 保存公告.
     * @throws ContainerExceptionInterface
     * @throws \Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function save(array $data): mixed
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

        // 待发送用户
        $userIds = $data['users'] ?? [];
        if (empty($userIds)) {
            $userMapper = container()->get(SystemUserMapper::class);
            $userIds = $userMapper->pluck(['status' => MineModel::ENABLE]);
        }
        $data['message_id'] = context_get('id') ?? (string) $this->idGenerator->generate();
        return parent::save($data);
    }
}
