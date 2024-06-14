<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

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
     * 删除组和所属配置.
     * @throws \Exception
     */
    public function deleteGroupAndConfig(mixed $id): bool
    {
        /**
         * @var SettingConfigGroup $model
         */
        $model = $this->read($id);
        $model->configs()->delete();
        return $model->delete();
    }
}
