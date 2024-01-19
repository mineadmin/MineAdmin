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

namespace App\Setting\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $name 配置组名称
 * @property string $code 配置组标识
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $remark 备注
 */
class SettingConfigGroup extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'setting_config_group';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'code', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * 关联config表.
     */
    public function configs(): HasMany
    {
        return $this->hasMany(SettingConfig::class, 'group_id', 'id');
    }
}
