<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id 主键
 * @property string $username 用户名
 * @property string|null $ip 登录IP地址
 * @property string|null $os 操作系统
 * @property string|null $browser 浏览器
 * @property int $status 登录状态 (1成功 2失败)
 * @property string|null $message 提示消息
 * @property Carbon $login_time 登录时间
 * @property string|null $remark 备注
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLoginLog whereUsername($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'username', 'ip', 'os', 'browser', 'status', 'message', 'login_time', 'remark'])]
class UserLoginLog extends Model
{
    public $timestamps = false;

    protected $table = 'user_login_log';

    protected static function booted(): void
    {
        self::creating(function (self $userLoginLog): void {
            $userLoginLog->login_time ??= now();
        });
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'status' => 'integer',
            'login_time' => 'datetime',
        ];
    }
}
