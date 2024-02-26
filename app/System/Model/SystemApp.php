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

namespace App\System\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $group_id 应用组ID
 * @property string $app_name 应用名称
 * @property string $app_id 应用ID
 * @property string $app_secret 应用密钥
 * @property int $status 状态 (1正常 2停用)
 * @property string $description 应用介绍
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property Collection|SystemApi[] $apis
 * @property SystemAppGroup $appGroup
 */
class SystemApp extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'system_app';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'group_id', 'app_name', 'app_id', 'app_secret', 'status', 'description', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'group_id' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * 通过中间表关联API.
     */
    public function apis(): BelongsToMany
    {
        return $this->belongsToMany(SystemApi::class, 'system_app_api', 'app_id', 'api_id');
    }

    /**
     * 关联APP分组.
     */
    public function appGroup(): HasOne
    {
        return $this->hasOne(SystemAppGroup::class, 'id', 'group_id');
    }
}
