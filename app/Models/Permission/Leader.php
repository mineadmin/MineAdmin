<?php

namespace App\Models\Permission;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $dept_id 部门ID
 * @property int $user_id 用户ID
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 删除时间
 * @property-read Department|null $department
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader whereDeptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leader withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[Fillable(['user_id', 'dept_id', 'created_at', 'updated_at', 'deleted_at'])]
class Leader extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'dept_leader';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'dept_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
