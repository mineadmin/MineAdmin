<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemDictTypeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 字典类型业务
 * Class SystemLoginLogService
 * @package App\System\Service
 */
class SystemDictTypeService extends AbstractService
{
    /**
     * @var SystemDictTypeMapper
     */
    public $mapper;


    public function __construct(SystemDictTypeMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
