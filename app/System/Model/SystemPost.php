<?php

declare (strict_types=1);
namespace App\System\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property string $name 岗位名称
 * @property string $code 岗位代码
 * @property int $sort 排序
 * @property int $status 状态 (1正常 2停用)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property-read \Hyperf\Database\Model\Collection|SystemUser[] $users 
 */
class SystemPost extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'system_post';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'name', 'code', 'sort', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 通过中间表获取用户
     */
    public function users() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'system_user_post', 'post_id', 'user_id');
    }
}