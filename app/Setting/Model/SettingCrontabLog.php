<?php

declare (strict_types=1);
namespace App\Setting\Model;

use Mine\MineModel;
/**
 * @property int $id 主键
 * @property int $crontab_id 任务ID
 * @property string $name 任务名称
 * @property string $target 任务调用目标字符串
 * @property string $parameter 任务调用参数
 * @property string $exception_info 异常信息
 * @property string $status 执行状态 (0成功 1失败)
 * @property string $created_at 创建时间
 */
class SettingCrontabLog extends MineModel
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting_crontab_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'crontab_id', 'name', 'target', 'parameter', 'exception_info', 'status', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'crontab_id' => 'integer'];
}