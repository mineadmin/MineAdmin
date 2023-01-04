<?php

declare (strict_types=1);
namespace App\System\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property int $parent_id 父ID
 * @property string $level 组级集合
 * @property string $name 部门名称
 * @property string $leader 负责人
 * @property string $phone 联系电话
 * @property int $status 状态 (1正常 2停用)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property-read \Hyperf\Database\Model\Collection|SystemRole[] $roles 
 */
class SystemDept extends MineModel
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'system_dept';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'leader', 'phone', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 通过中间表获取角色
     */
    public function roles() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_role_dept', 'dept_id', 'role_id');
    }
    /**
     * 通过中间表关联部门
     * @return \Hyperf\Database\Model\Relations\BelongsToMany
     */
    public function users() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'system_user_dept', 'dept_id', 'user_id');
    }
    /**
     * 通过中间表关联部门
     * @return \Hyperf\Database\Model\Relations\BelongsToMany
     */
    public function leader() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'system_dept_leader', 'dept_id', 'user_id');
    }
}