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

namespace App\Repository\Settings;

use App\Model\Settings\ConfigGroup;
use App\Kernel\IRepository\AbstractRepository;

class ConfigGroupRepository extends AbstractRepository
{
    /**
     * @var ConfigGroup
     */
    public $model;

    public function assignModel()
    {
        $this->model = ConfigGroup::class;
    }

    /**
     * 删除组和所属配置.
     * @throws \Exception
     */
    public function deleteGroupAndConfig(mixed $id): bool
    {
        /**
         * @var ConfigGroup $model
         */
        $model = $this->read($id);
        $model->configs()->delete();
        return $model->delete();
    }
}
