<?php

declare (strict_types=1);
namespace App\System\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property int $group_id 应用组ID
 * @property string $app_name 应用名称
 * @property string $app_id 应用ID
 * @property string $app_secret 应用密钥
 * @property int $status 状态 (1正常 2停用)
 * @property string $description 应用介绍
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property-read \Hyperf\Database\Model\Collection|SystemApi[] $apis 
 * @property-read SystemAppGroup $appGroup 
 */
class SystemApp extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'system_app';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'group_id', 'app_name', 'app_id', 'app_secret', 'status', 'description', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'group_id' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 通过中间表关联API
     * @return \Hyperf\Database\Model\Relations\BelongsToMany
     */
    public function apis() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemApi::class, 'system_app_api', 'app_id', 'api_id');
    }
    /**
     * 关联APP分组
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function appGroup() : \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(SystemAppGroup::class, 'id', 'group_id');
    }
}