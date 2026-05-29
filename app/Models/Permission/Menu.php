<?php

namespace App\Models\Permission;

use App\Models\Casts\MetaCast;
use App\Models\Enums\User\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id 主键
 * @property int $parent_id 父ID
 * @property string $name 菜单名称
 * @property Meta|null $meta 附加属性
 * @property string $path 路径
 * @property string $component 组件路径
 * @property string $redirect 重定向地址
 * @property Status $status 状态:1=正常,2=停用
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property string $remark 备注
 * @property-read Collection<int, Menu> $children
 * @property-read int|null $children_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedBy($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'parent_id', 'name', 'component', 'redirect', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark', 'meta', 'path'])]
final class Menu extends Model
{
    protected $table = 'menu';

    protected static function booted(): void
    {
        self::deleting(function (self $menu): void {
            $menu->roles()->detach();
        });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_belongs_menu', 'menu_id', 'role_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->where('status', Status::Normal)
            ->orderBy('sort')
            ->with('children');
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'parent_id' => 'integer',
            'status' => Status::class,
            'sort' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'meta' => MetaCast::class,
            'path' => 'string',
        ];
    }
}
