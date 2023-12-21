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

namespace App\Setting\Model;

use Mine\MineModel;

/**
 * @property int $id 主键
 * @property int $crontab_id 任务ID
 * @property string $name 任务名称
 * @property string $target 任务调用目标字符串
 * @property string $parameter 任务调用参数
 * @property string $exception_info 异常信息
 * @property int $status 执行状态 (1成功 2失败)
 * @property string $created_at 创建时间
 */
class SettingCrontabLog extends MineModel
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'setting_crontab_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'crontab_id', 'name', 'target', 'parameter', 'exception_info', 'status', 'created_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'crontab_id' => 'integer', 'status' => 'integer'];
}
