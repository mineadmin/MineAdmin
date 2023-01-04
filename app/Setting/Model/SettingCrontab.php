<?php

declare (strict_types=1);
namespace App\Setting\Model;

use Mine\MineModel;
/**
 * @property int $id 主键
 * @property string $name 任务名称
 * @property int $type 任务类型 (1 command, 2 class, 3 url, 4 eval)
 * @property string $target 调用任务字符串
 * @property string $parameter 调用任务参数
 * @property string $rule 任务执行表达式
 * @property int $singleton 是否单次执行 (1 是 2 不是)
 * @property int $status 状态 (1正常 2停用)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $remark 备注
 * @property-read \Hyperf\Database\Model\Collection|SettingCrontabLog[] $logs 
 */
class SettingCrontab extends MineModel
{
    // 命令任务
    public const COMMAND_CRONTAB = 1;
    // 类任务
    public const CLASS_CRONTAB = 2;
    // URL任务
    public const URL_CRONTAB = 3;
    // EVAL 任务
    public const EVAL_CRONTAB = 4;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'setting_crontab';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'name', 'type', 'target', 'parameter', 'rule', 'singleton', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'type' => 'integer', 'singleton' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 关联字典任务日志表
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function logs() : \Hyperf\Database\Model\Relations\HasMany
    {
        return $this->hasMany(SettingCrontabLog::class, 'crontab_id', 'id');
    }
}