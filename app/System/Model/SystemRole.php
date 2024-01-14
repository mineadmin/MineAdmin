<?php

declare (strict_types=1);
namespace App\System\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property string $name 角色名称
 * @property string $code 角色代码
 * @property int $data_scope 数据范围（1：全部数据权限 2：自定义数据权限 3：本部门数据权限 4：本部门及以下数据权限 5：本人数据权限）
 * @property int $status 状态 (1正常 2停用)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property-read \Hyperf\Database\Model\Collection|SystemDept[] $depts 
 * @property-read \Hyperf\Database\Model\Collection|SystemMenu[] $menus 
 * @property-read \Hyperf\Database\Model\Collection|SystemUser[] $users 
 */
class SystemRole extends MineModel
{
    use SoftDeletes;
    // 所有
    public const ALL_SCOPE = 1;
    // 自定义
    public const CUSTOM_SCOPE = 2;
    // 本部门
    public const SELF_DEPT_SCOPE = 3;
    // 本部门及子部门
    public const DEPT_BELOW_SCOPE = 4;
    // 本人
    public const SELF_SCOPE = 5;
    // 本部门及子部门，通过表的部门id
    public const DEPT_BELOW_SCOPE_BY_TABLE_DEPTID = 6;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'system_role';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'name', 'code', 'data_scope', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'data_scope' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 通过中间表获取菜单
     */
    public function menus() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemMenu::class, 'system_role_menu', 'role_id', 'menu_id');
    }
    /**
     * 通过中间表获取用户
     */
    public function users() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'system_user_role', 'role_id', 'user_id');
    }
    /**
     * 通过中间表获取部门
     */
    public function depts() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemDept::class, 'system_role_dept', 'role_id', 'dept_id');
    }
}