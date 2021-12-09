<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemApiLogMapper;
use Mine\Abstracts\AbstractService;

/**
 * api日志业务
 * Class SystemAppService
 * @package App\System\Service
 */
class SystemApiLogService extends AbstractService
{
    /**
     * @var SystemApiLogMapper
     */
    public $mapper;

    public function __construct(SystemApiLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}