<?php

namespace App\Models\Permission;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as BaseCollection;

/**
 * @property int $id 主键
 * @property string $name 部门名称
 * @property int $parent_id 父级部门ID
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 删除时间
 * @property-read Collection<int, Department> $children
 * @property-read int|null $children_count
 * @property-read Collection<int, User> $departmentUsers
 * @property-read int|null $department_users_count
 * @property-read Collection<int, User> $leader
 * @property-read int|null $leader_count
 * @property-read Collection<int, Position> $positions
 * @property-read int|null $positions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'name', 'parent_id', 'created_at', 'updated_at', 'deleted_at'])]
class Department extends Model
{
    use SoftDeletes;

    protected $table = 'department';

    protected static function booted(): void
    {
        self::deleted(function (self $department): void {
            $department->positions()->delete();
            $department->departmentUsers()->detach();
            $department->leader()->detach();
        });
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'dept_id', 'id');
    }

    public function departmentUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_dept', 'dept_id', 'user_id');
    }

    public function leader(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dept_leader', 'dept_id', 'user_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->with(['children', 'positions']);
    }

    public function getFlatChildren(): BaseCollection
    {
        $flat = collect();
        $this->load('children');
        $traverse = static function ($departments) use (&$traverse, $flat): void {
            foreach ($departments as $department) {
                $flat->push($department);
                if ($department->children->isNotEmpty()) {
                    $traverse($department->children);
                }
            }
        };
        $traverse($this->children);

        return $flat->prepend($this);
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'parent_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
