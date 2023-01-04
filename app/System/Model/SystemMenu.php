<?php

declare (strict_types=1);
namespace App\System\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property int $parent_id 父ID
 * @property string $level 组级集合
 * @property string $name 菜单名称
 * @property string $code 菜单标识代码
 * @property string $icon 菜单图标
 * @property string $route 路由地址
 * @property string $component 组件路径
 * @property string $redirect 跳转地址
 * @property int $is_hidden 是否隐藏 (1是 2否)
 * @property string $type 菜单类型, (M菜单 B按钮 L链接 I iframe)
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
class SystemMenu extends MineModel
{
    use SoftDeletes;
    /**
     * 类型
     */
    public const LINK = 'L';
    public const IFRAME = 'I';
    public const MENUS_LIST = 'M';
    public const BUTTON = 'B';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'system_menu';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'code', 'icon', 'route', 'component', 'redirect', 'is_hidden', 'type', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'is_hidden' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 通过中间表获取角色
     */
    public function roles() : \Hyperf\Database\Model\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_role_menu', 'menu_id', 'role_id');
    }
}