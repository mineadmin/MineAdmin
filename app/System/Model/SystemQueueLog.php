<?php

declare (strict_types=1);
namespace App\System\Model;

use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;
/**
 * @property int $id 主键
 * @property string $exchange_name 交换机名称
 * @property string $routing_key_name 路由名称
 * @property string $queue_name 队列名称
 * @property string $queue_content 队列数据
 * @property string $log_content 队列日志
 * @property string $produce_status 生产状态 0:未生产 1:生产中 2:生产成功 3:生产失败 4:生产重复
 * @property string $consume_status 消费状态 0:未消费 1:消费中 2:消费成功 3:消费失败 4:消费重复
 * @property int $delay_time 延迟时间（秒）
 * @property int $created_by 创建者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class SystemQueueLog extends MineModel
{
    /**
     * @Message("未生产")
     */
    const PRODUCE_STATUS_WAITING = 0;
    /**
     * @Message("生产中")
     */
    const PRODUCE_STATUS_DOING = 1;
    /**
     * @Message("生产成功")
     */
    const PRODUCE_STATUS_SUCCESS = 2;
    /**
     * @Message("生产失败")
     */
    const PRODUCE_STATUS_FAIL = 3;
    /**
     * @Message("生产重复")
     */
    const PRODUCE_STATUS_4 = 4;
    /**
     * @Message("未消费")
     */
    const CONSUME_STATUS_0 = 0;
    /**
     * @Message("消费中")
     */
    const CONSUME_STATUS_DOING = 1;
    /**
     * @Message("消费成功")
     */
    const CONSUME_STATUS_SUCCESS = 2;
    /**
     * @Message("消费失败")
     */
    const CONSUME_STATUS_FAIL = 3;
    /**
     * @Message("消费重复")
     */
    const CONSUME_STATUS_4 = 4;

    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_queue_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'exchange_name', 'routing_key_name', 'queue_name', 'queue_content', 'log_content', 'produce_status', 'consume_status', 'delay_time', 'created_by', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'delay_time' => 'integer', 'created_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}