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

use App\Model\Casts\MetaCast;
use App\Model\Enums\User\Status;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Events\Deleting;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $id 主键
 * @property int $parent_id 父ID
 * @property string $name 菜单名称
 * @property string $component 组件路径
 * @property string $redirect 跳转地址
 * @property string $path 地址
 * @property int $status 状态 (1正常 2停用)
 * @property Meta $meta 附加属性
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间]
 * @property string $remark 备注
 * @property Collection|Role[] $roles
 * @property Collection|Menu[] $children
 */
final class Menu extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'menu';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'parent_id', 'name',
        'component', 'redirect',
        'status', 'sort',
        'created_by', 'updated_by',
        'created_at', 'updated_at',
        'remark', 'meta', 'path',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer',
        'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer',
        'created_at' => 'datetime', 'updated_at' => 'datetime', 'meta' => MetaCast::class,
        'path' => 'string',
    ];

    /**
     * 通过中间表获取角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_belongs_menu',
            'menu_id',
            'role_id'
        );
    }

    public function children()
    {
        // @phpstan-ignore-next-line
        return $this
            ->hasMany(self::class, 'parent_id', 'id')
            ->where('status', Status::Normal)
            ->orderBy('sort')
            ->with('children');
    }

    public function deleting(Deleting $event)
    {
        $this->roles()->detach();
    }
}
