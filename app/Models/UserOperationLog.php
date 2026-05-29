<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $method 请求方式
 * @property string $router 请求路由
 * @property string $service_name 业务名称
 * @property string|null $ip 请求IP地址
 * @property string|null $ip_location IP归属地
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property string|null $remark 备注
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereIpLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereRouter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereServiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserOperationLog whereUsername($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'username', 'method', 'router', 'service_name', 'ip', 'ip_location', 'created_at', 'updated_at', 'remark'])]
class UserOperationLog extends Model
{
    protected $table = 'user_operation_log';

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
