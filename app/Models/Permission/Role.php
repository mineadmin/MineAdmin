<?php

namespace App\Models\Permission;

use App\Models\Enums\User\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id 主键
 * @property string $name 角色名称
 * @property string $code 角色代码
 * @property int $data_scope 数据范围
 * @property Status $status 状态:1=正常,2=停用
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property string $remark 备注
 * @property-read Collection<int, Menu> $menus
 * @property-read int|null $menus_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDataScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedBy($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'name', 'code', 'data_scope', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'])]
final class Role extends Model
{
    protected $table = 'role';

    protected static function booted(): void
    {
        self::deleting(function (self $role): void {
            $role->users()->detach();
            $role->menus()->detach();
        });
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_belongs_menu', 'role_id', 'menu_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_belongs_role', 'role_id', 'user_id');
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'data_scope' => 'integer',
            'status' => Status::class,
            'sort' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
