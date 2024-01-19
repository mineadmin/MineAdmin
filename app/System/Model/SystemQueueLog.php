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
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $exchange_name 交换机名称
 * @property string $routing_key_name 路由名称
 * @property string $queue_name 队列名称
 * @property string $queue_content 队列数据
 * @property string $log_content 队列日志
 * @property int $produce_status 生产状态 1:未生产 2:生产中 3:生产成功 4:生产失败 5:生产重复
 * @property int $consume_status 消费状态 1:未消费 2:消费中 3:消费成功 4:消费失败 5:消费重复
 * @property int $delay_time 延迟时间（秒）
 * @property int $created_by 创建者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
class SystemQueueLog extends MineModel
{
    /**
     * @Message("未生产")
     */
    public const PRODUCE_STATUS_WAITING = 1;

    /**
     * @Message("生产中")
     */
    public const PRODUCE_STATUS_DOING = 2;

    /**
     * @Message("生产成功")
     */
    public const PRODUCE_STATUS_SUCCESS = 3;

    /**
     * @Message("生产失败")
     */
    public const PRODUCE_STATUS_FAIL = 4;

    /**
     * @Message("生产重复")
     */
    public const PRODUCE_STATUS_REPEAT = 5;

    /**
     * @Message("未消费")
     */
    public const CONSUME_STATUS_NO = 1;

    /**
     * @Message("消费中")
     */
    public const CONSUME_STATUS_DOING = 2;

    /**
     * @Message("消费成功")
     */
    public const CONSUME_STATUS_SUCCESS = 3;

    /**
     * @Message("消费失败")
     */
    public const CONSUME_STATUS_FAIL = 4;

    /**
     * @Message("消费重复")
     */
    public const CONSUME_STATUS_REPEAT = 5;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'system_queue_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'exchange_name', 'routing_key_name', 'queue_name', 'queue_content', 'log_content', 'produce_status', 'consume_status', 'delay_time', 'created_by', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'produce_status' => 'integer', 'consume_status' => 'integer', 'delay_time' => 'integer', 'created_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
