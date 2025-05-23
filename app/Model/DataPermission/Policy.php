<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Model\DataPermission;

use App\Model\Enums\DataPermission\PolicyType;
use App\Model\Permission\Position;
use App\Model\Permission\User;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id 用户ID（与角色二选一）
 * @property int $position_id 岗位ID（与用户二选一）
 * @property PolicyType $policy_type 策略类型（DEPT_SELF, DEPT_TREE, ALL, SELF, CUSTOM_DEPT, CUSTOM_FUNC）
 * @property bool $is_default 是否默认策略（默认值：true）
 * @property array $value 策略值
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Collection<int,Position>|Position[] $positions
 * @property Collection<int,User>|User[] $users
 */
class Policy extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'data_permission_policy';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'position_id', 'policy_type', 'is_default', 'created_at', 'updated_at', 'deleted_at', 'value'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 'user_id' => 'integer', 'position_id' => 'integer',
        'is_default' => 'bool', 'created_at' => 'datetime',
        'updated_at' => 'datetime', 'deleted_at' => 'datetime',
        'policy_type' => PolicyType::class, 'value' => 'array',
    ];

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'data_permission_policy_position', 'policy_id', 'position_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'data_permission_policy_user', 'policy_id', 'user_id');
    }
}
