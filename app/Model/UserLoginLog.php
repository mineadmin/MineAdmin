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

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $ip 登录IP地址
 * @property string $os 操作系统
 * @property string $browser 浏览器
 * @property int $status 登录状态 (1成功 2失败)
 * @property string $message 提示消息
 * @property Carbon $login_time 登录时间
 * @property string $remark 备注
 */
class UserLoginLog extends Model
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_login_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'ip', 'os', 'browser', 'status', 'message', 'login_time', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'login_time' => 'datetime'];

    public function creating(Creating $event)
    {
        if ($event->getModel()->login_time === null) {
            $event->getModel()->login_time = Carbon::now();
        }
    }
}
