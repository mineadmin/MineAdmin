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

namespace App\Model\Permission;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $user_id 用户ID
 * @property int $dept_id 部门ID
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property null|Department $department
 * @property null|User $user
 */
class Leader extends Model
{
    use SoftDeletes;

    public bool $incrementing = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'dept_leader';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'dept_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'dept_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
