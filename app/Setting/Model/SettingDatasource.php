<?php

declare(strict_types=1);

namespace App\Setting\Model;

use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $name 数据源名称
 * @property string $db_driver 数据库驱动
 * @property string $db_hosthost  数据库地址
 * @property string $db_port 数据库端口
 * @property string $db_name 数据库名称
 * @property string $db_user 数据库用户
 * @property string $db_pass 数据库密码
 * @property string $db_charset 数据库字符集
 * @property string $db_collation 数据库字符序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $remark 备注
 */
class SettingDatasource extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'setting_datasource';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'db_driver', 'db_host', 'db_port', 'db_name', 'db_user', 'db_pass', 'db_charset', 'db_collation', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
