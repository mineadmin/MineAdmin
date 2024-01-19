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

use Mine\MineModel;

/**
 * @property int $group_id 组id
 * @property string $key 配置键名
 * @property string $value 配置值
 * @property string $name 配置名称
 * @property string $input_type 数据输入类型
 * @property string $config_select_data 配置选项数据
 * @property int $sort 排序
 * @property string $remark 备注
 */
class SettingConfig extends MineModel
{
    public bool $incrementing = false;

    public bool $timestamps = false;

    protected string $primaryKey = 'key';

    protected string $keyType = 'string';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'setting_config';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['group_id', 'key', 'value', 'name', 'input_type', 'config_select_data', 'sort', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['group_id' => 'integer', 'sort' => 'integer', 'config_select_data' => 'array'];
}
