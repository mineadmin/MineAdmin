<?php

declare(strict_types=1);
namespace App\Setting\Mapper;

use App\Setting\Model\SettingConfigGroup;
use Mine\Abstracts\AbstractMapper;

class SettingConfigGroupMapper extends AbstractMapper
{
    /**
     * @var SettingConfigGroup
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingConfigGroup::class;
    }

    /**
     * 删除组和所属配置
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteGroupAndConfig(int $id): bool
    {
        /* @var $model SettingConfigGroup */
        $model = $this->read($id);
        $model->configs()->delete();
        return $model->delete();
    }
}