<?php

declare(strict_types = 1);
namespace App\System\Service;

use App\System\Mapper\SystemQueueMapper;
use Mine\Abstracts\AbstractService;

/**
 * 队列管理服务类
 */
class SystemQueueService extends AbstractService
{
    /**
     * @var SystemQueueMapper
     */
    public $mapper;

    public function __construct(SystemQueueMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}