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
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $api_id 接口主键
 * @property string $name 字段名称
 * @property int $type 字段类型 1 请求 2 返回
 * @property string $data_type 数据类型
 * @property int $is_required 是否必填 1 非必填 2 必填
 * @property string $default_value 默认值
 * @property int $status 状态 (1正常 2停用)
 * @property string $description 字段说明
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property SystemApi $api
 */
class SystemApiColumn extends MineModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'system_api_column';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'api_id', 'name', 'type', 'data_type', 'is_required', 'default_value', 'status', 'description', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'api_id' => 'integer', 'type' => 'integer', 'is_required' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * 关联API.
     */
    public function api(): BelongsTo
    {
        return $this->belongsTo(SystemApi::class, 'api_id', 'id');
    }
}
