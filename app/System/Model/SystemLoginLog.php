<?php

declare (strict_types=1);
namespace App\System\Model;

use Mine\MineModel;
/**
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $ip 登录IP地址
 * @property string $ip_location IP所属地
 * @property string $os 操作系统
 * @property string $browser 浏览器
 * @property int $status 登录状态 (1成功 2失败)
 * @property string $message 提示消息
 * @property string $login_time 登录时间
 * @property string $remark 备注
 */
class SystemLoginLog extends MineModel
{
    public bool $timestamps = false;
    public const SUCCESS = 1;
    public const FAIL = 2;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'system_login_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'username', 'ip', 'ip_location', 'os', 'browser', 'status', 'message', 'login_time', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer'];
}