<?php

declare(strict_types=1);
namespace App\Setting\Service;


use App\Setting\Mapper\SettingConfigGroupMapper;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;

class SettingConfigGroupService extends AbstractService
{
    /**
     * @var SettingConfigGroupMapper
     */
    public $mapper;

    /**
     * SettingConfigGroupService constructor.
     * @param SettingConfigGroupMapper $mapper
     */
    public function __construct(SettingConfigGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 删除配置组和其所属配置
     * @param int $id
     * @return bool
     */
    #[Transaction]
    public function deleteConfigGroup(int $id): bool
    {
        return $this->mapper->deleteGroupAndConfig($id);
    }
}