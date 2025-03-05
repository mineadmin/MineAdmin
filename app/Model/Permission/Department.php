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
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $name 部门名称
 * @property int $parent_id 父级部门ID
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Collection<int,Position>|Position[] $positions 岗位
 * @property Collection<int,User>|User[] $department_users 部门用户
 * @property Collection<int,User>|User[] $leader 部门领导
 */
class Department extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'department';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'parent_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime'];

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'dept_id', 'id');
    }

    public function department_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_dept', 'dept_id', 'user_id');
    }

    public function leaders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_leader', 'dept_id', 'user_id');
    }
}
