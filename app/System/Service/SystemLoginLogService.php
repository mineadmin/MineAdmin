<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemLoginLogMapper;
use Mine\Abstracts\AbstractService;

/**
 * 登录日志业务
 * Class SystemLoginLogService
 * @package App\System\Service
 */
class SystemLoginLogService extends AbstractService
{
    /**
     * @var SystemLoginLogMapper
     */
    public $mapper;

    public function __construct(SystemLoginLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}