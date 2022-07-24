<?php

declare(strict_types=1);
namespace App\Setting\Service;


use App\Setting\Mapper\SettingConfigGroupMapper;
use Mine\Abstracts\AbstractService;

class SettingConfigGroupService extends AbstractService
{
    /**
     * @var SettingConfigGroupMapper
     */
    public $mapper;

    /**
     * SettingConfigGroupService constructor.
     * @param SettingConfigMapper $mapper
     */
    public function __construct(SettingConfigGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}