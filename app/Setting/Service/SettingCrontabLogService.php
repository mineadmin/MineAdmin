<?php


namespace App\Setting\Service;


use App\Setting\Mapper\SettingCrontabLogMapper;
use Mine\Abstracts\AbstractService;

class SettingCrontabLogService extends AbstractService
{
    /**
     * @var SettingCrontabLogMapper
     */
    public $mapper;

    public function __construct(SettingCrontabLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}