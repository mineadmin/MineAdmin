<?php

declare(strict_types = 1);
namespace App\System\Service;


use App\System\Mapper\SystemOperLogMapper;
use Mine\Abstracts\AbstractService;

class SystemOperLogService extends AbstractService
{
    public $mapper;

    public function __construct(SystemOperLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}