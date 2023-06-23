<?php

declare(strict_types = 1);
namespace App\System\Service;


use App\System\Mapper\SystemOperLogMapper;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Interfaces\serviceInterface\OperLogServiceInterface;

#[DependProxy(values: [ OperLogServiceInterface::class ])]
class SystemOperLogService extends AbstractService implements OperLogServiceInterface
{
    public $mapper;

    public function __construct(SystemOperLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}