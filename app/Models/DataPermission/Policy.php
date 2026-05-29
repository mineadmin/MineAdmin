<?php

namespace App\Models\DataPermission;

use App\Models\Enums\DataPermission\PolicyType;
use App\Models\Permission\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id 主键
 * @property int $user_id 用户ID（与角色二选一）
 * @property int $position_id 岗位ID（与用户二选一）
 * @property PolicyType $policy_type 策略类型（DEPT_SELF, DEPT_TREE, ALL, SELF, CUSTOM_DEPT, CUSTOM_FUNC）
 * @property bool $is_default 是否默认策略（默认值：true）
 * @property array<array-key, mixed>|null $value 策略值
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 删除时间
 * @property-read Collection<int, Position> $positions
 * @property-read int|null $positions_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy wherePolicyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'user_id', 'position_id', 'policy_type', 'is_default', 'created_at', 'updated_at', 'deleted_at', 'value'])]
class Policy extends Model
{
    use SoftDeletes;

    protected $table = 'data_permission_policy';

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'data_permission_policy_position', 'policy_id', 'position_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'data_permission_policy_user', 'policy_id', 'user_id');
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'position_id' => 'integer',
            'is_default' => 'bool',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'policy_type' => PolicyType::class,
            'value' => 'array',
        ];
    }
}
