<?php

declare (strict_types=1);
namespace App\Setting\Model;

use Mine\MineModel;
/**
 * @property int $group_id 组ID
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
    protected string $primaryKey = 'key';
    protected string $keyType = 'string';
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'setting_config';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['group_id', 'key', 'value', 'name', 'input_type', 'config_select_data', 'sort', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['group_id' => 'integer', 'sort' => 'integer', 'config_select_data' => 'array'];
}