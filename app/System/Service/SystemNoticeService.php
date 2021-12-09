<?php

declare(strict_types = 1);
namespace App\System\Service;

use App\Amqp\Producer\MessageProducer;
use App\System\Mapper\SystemNoticeMapper;
use Mine\Abstracts\AbstractService;
use Hyperf\Di\Annotation\Inject;
use Mine\Amqp\DelayProducer;

/**
 * 通知管理服务类
 */
class SystemNoticeService extends AbstractService
{
    /**
     * @var SystemNoticeMapper
     */
    public $mapper;

    /**
     * @Inject
     * @var DelayProducer
     */
    protected $producer;

    public function __construct(SystemNoticeMapper $mapper)
    {
//        $message = new NoticeProducer('message');
//        $this->producer->produce($message,false,5,5);
        $this->mapper = $mapper;
    }
    
}
