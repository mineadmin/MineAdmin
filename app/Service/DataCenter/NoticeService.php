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

namespace App\Service\DataCenter;

use App\Async\Job\Vo\QueueMessageVo;
use App\Model\DataCenter\QueueMessage;
use App\Repository\DataCenter\NoticeRepository;
use App\Repository\Permission\UserRepository;
use Hyperf\Snowflake\IdGenerator\SnowflakeIdGenerator;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\MineModel;

/**
 * 通知管理服务类.
 */
class NoticeService extends AbstractService
{
    /**
     * @var NoticeRepository
     */
    public $repository;

    public function __construct(
        NoticeRepository $repository,
        private readonly SnowflakeIdGenerator $idGenerator
    ) {
        $this->repository = $repository;
    }

    /**
     * 保存公告.
     */
    #[Transaction]
    public function save(array $data): mixed
    {
        $message = new QueueMessageVo();
        $message->setTitle($data['title']);
        $message->setContentType(
            $data['type'] === '1'
                ? QueueMessage::TYPE_NOTICE
                : QueueMessage::TYPE_ANNOUNCE
        );
        $message->setContent($data['content']);
        $message->setSendBy(user()->getId());

        // 待发送用户
        $userIds = $data['users'] ?? [];
        if (empty($userIds)) {
            $userRepository = container()->get(UserRepository::class);
            $userIds = $userRepository->pluck(['status' => MineModel::ENABLE]);
        }
        $data['message_id'] = context_get('id') ?? (string) $this->idGenerator->generate();
        return parent::save($data);
    }
}
