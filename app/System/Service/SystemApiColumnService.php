<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemApiColumnMapper;
use Mine\Abstracts\AbstractService;

/**
 * api接口字段业务
 * Class SystemApiColumnService
 * @package App\System\Service
 */
class SystemApiColumnService extends AbstractService
{
    /**
     * @var SystemApiColumnMapper
     */
    public $mapper;

    public function __construct(SystemApiColumnMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}